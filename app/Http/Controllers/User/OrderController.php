<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Game;
use App\Models\Package;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->orders()->with(['game', 'package'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);

        return view('user.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['game', 'package', 'payment'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('user.orders.show', compact('order'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'package_id' => 'required|exists:packages,id',
        ]);

        $game = Game::findOrFail($request->game_id);
        $package = Package::where('game_id', $game->id)
            ->where('id', $request->package_id)
            ->firstOrFail();

        return view('user.orders.create', compact('game', 'package'));
    }

    public function store(Request $request)
    {
        try {
            $game = Game::findOrFail($request->game_id);
            $isMLBB = stripos($game->name, 'Mobile Legends') !== false || stripos($game->name, 'MLBB') !== false;
            
            // Build validation rules
            $rules = [
                'game_id' => 'required|exists:games,id',
                'package_id' => 'required|string', // Can be local package ID or G2Bulk package ID (g2bulk_xxx)
                'user_id' => 'required|string|max:255',
                'payment_method' => 'required|in:wallet,wavepay,kpay,manual',
            ];

            // Add server_id validation based on game type
            if ($isMLBB || $game->requires_server) {
                $rules['server_id'] = 'required|string|max:255';
            } else {
                $rules['server_id'] = 'nullable|string|max:255';
            }

            $validated = $request->validate($rules, [
                'user_id.required' => $isMLBB ? 'Game ID is required' : 'User ID is required',
                'server_id.required' => $isMLBB ? 'Zone ID is required for Mobile Legends' : 'Server ID is required for this game',
                'payment_method.required' => 'Please select a payment method',
            ]);

            // Check if package is from G2Bulk (starts with 'g2bulk_')
            $isG2BulkPackage = strpos($request->package_id, 'g2bulk_') === 0;
            
            if ($isG2BulkPackage) {
                // Get G2Bulk catalogue data from request or fetch from API
                $g2bulkService = new \App\Services\G2BulkService();
                $gameCode = $this->getGameCode($game->name);
                $catalogueResult = $g2bulkService->getGameCatalogue($gameCode);
                
                if (!$catalogueResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to fetch package details from G2Bulk',
                    ], 422);
                }
                
                // Find the catalogue by ID (remove 'g2bulk_' prefix)
                $catalogueId = str_replace('g2bulk_', '', $request->package_id);
                $catalogues = $catalogueResult['catalogues'] ?? [];
                $selectedCatalogue = collect($catalogues)->firstWhere('id', $catalogueId);
                
                if (!$selectedCatalogue) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Package not found in G2Bulk catalogue',
                    ], 422);
                }
                
                // Calculate price in Ks
                $exchangeRate = \App\Models\Setting::get('usd_to_kyat_rate', 2100);
                $priceInKs = ($selectedCatalogue['amount'] ?? 0) * $exchangeRate;
                
                // Create order with G2Bulk package info
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'game_id' => $game->id,
                    'package_id' => null, // No local package ID for G2Bulk packages
                    'user_game_id' => trim($request->user_id),
                    'server_id' => $request->server_id ? trim($request->server_id) : null,
                    'amount' => $priceInKs,
                    'payment_method' => $request->payment_method,
                    'status' => 'pending',
                    'api_response' => [
                        'g2bulk_catalogue' => $selectedCatalogue,
                        'game_code' => $gameCode,
                    ],
                ]);
            } else {
                // Local package
                $package = Package::where('game_id', $game->id)
                    ->where('id', $request->package_id)
                    ->firstOrFail();
                
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'game_id' => $game->id,
                    'package_id' => $package->id,
                    'user_game_id' => trim($request->user_id),
                    'server_id' => $request->server_id ? trim($request->server_id) : null,
                    'amount' => $package->price,
                    'payment_method' => $request->payment_method,
                    'status' => 'pending',
                ]);
            }

            // Check if user has enough balance for wallet payment
            if ($request->payment_method === 'wallet') {
                if (!auth()->check()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please login to continue',
                    ], 401);
                }
                
                if (!method_exists(auth()->user(), 'hasEnoughBalance') || !auth()->user()->hasEnoughBalance($package->price)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient wallet balance',
                    ], 422);
                }
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'game_id' => $game->id,
                'package_id' => $package->id,
                'user_game_id' => trim($request->user_id),
                'server_id' => $request->server_id ? trim($request->server_id) : null,
                'amount' => $package->price,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);

            // Create payment record
            $order->payment()->create([
                'user_id' => auth()->id(),
                'method' => $request->payment_method,
                'amount' => $package->price,
                'status' => $request->payment_method === 'wallet' ? 'approved' : 'pending',
            ]);

            // Process wallet payment immediately
            if ($request->payment_method === 'wallet') {
                if (method_exists(auth()->user(), 'deductBalance')) {
                    auth()->user()->deductBalance(
                        $package->price,
                        "Order #{$order->order_id}",
                        Order::class,
                        $order->id
                    );
                }
                
                // Process order via queue
                if (class_exists(\App\Jobs\ProcessOrder::class)) {
                    dispatch(new \App\Jobs\ProcessOrder($order));
                }
            }

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Order creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the order. Please try again.',
            ], 500);
        }
    }

    public function updateAccountInfo(Request $request, $id)
    {
        $order = Order::where('user_id', auth()->id())
            ->findOrFail($id);

        // Only allow update if order is pending or failed
        if (!in_array($order->status, ['pending', 'failed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update account information for completed or processing orders',
            ], 422);
        }

        $request->validate([
            'user_game_id' => 'required|string|max:255',
            'server_id' => $order->game->requires_server ? 'required|string|max:255' : 'nullable|string|max:255',
        ]);

        $order->update([
            'user_game_id' => $request->user_game_id,
            'server_id' => $request->server_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account information updated successfully',
        ]);
    }
}

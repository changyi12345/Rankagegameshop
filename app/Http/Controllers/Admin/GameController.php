<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Package;
use App\Services\G2BulkService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::withCount(['orders', 'packages'])->orderBy('sort_order')->get();
        return view('admin.games.index', compact('games'));
    }

    public function create()
    {
        return view('admin.games.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:10',
            'currency_name' => 'required|string|max:50',
            'requires_server' => 'boolean',
            'profit_margin' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        Game::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Game created successfully',
        ]);
    }

    public function edit($id)
    {
        $game = Game::findOrFail($id);
        return view('admin.games.edit', compact('game'));
    }

    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:10',
            'currency_name' => 'required|string|max:50',
            'requires_server' => 'boolean',
            'profit_margin' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $game->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Game updated successfully',
        ]);
    }

    public function toggle($id, Request $request)
    {
        $game = Game::findOrFail($id);
        $game->update(['is_active' => $request->is_active]);

        return response()->json(['success' => true]);
    }

    public function packages($id)
    {
        $game = Game::with('allPackages')->findOrFail($id);
        $packages = $game->allPackages;
        
        // Get game code for G2Bulk API
        $gameCode = $this->getGameCode($game->name);
        
        // Fetch G2Bulk catalogue
        $g2bulkService = new G2BulkService();
        $catalogueResult = $g2bulkService->getGameCatalogue($gameCode);
        $g2bulkCatalogues = $catalogueResult['success'] ? ($catalogueResult['catalogues'] ?? []) : [];
        
        // Get exchange rate
        $exchangeRate = \App\Models\Setting::get('usd_to_kyat_rate', 2100);
        
        return view('admin.games.packages', compact('game', 'packages', 'g2bulkCatalogues', 'gameCode', 'exchangeRate'));
    }
    
    /**
     * Fetch packages from G2Bulk API
     */
    public function fetchG2BulkPackages($id)
    {
        try {
            $game = Game::findOrFail($id);
            $gameCode = $this->getGameCode($game->name);
            
            $g2bulkService = new G2BulkService();
            $catalogueResult = $g2bulkService->getGameCatalogue($gameCode);
            
            if (!$catalogueResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $catalogueResult['message'] ?? 'Failed to fetch packages from G2Bulk',
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'catalogues' => $catalogueResult['catalogues'] ?? [],
                'game' => $catalogueResult['game'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Import G2Bulk package to local database
     */
    public function importG2BulkPackage(Request $request, $id)
    {
        $request->validate([
            'catalogue_id' => 'required|integer',
            'catalogue_name' => 'required|string',
            'catalogue_amount' => 'required|numeric',
        ]);
        
        try {
            $game = Game::findOrFail($id);
            $exchangeRate = \App\Models\Setting::get('usd_to_kyat_rate', 2100);
            
            // Calculate price in Ks
            $priceInKs = $request->catalogue_amount * $exchangeRate;
            
            // Extract currency amount from catalogue name (e.g., "310 Diamonds" -> 310)
            $currencyAmount = preg_match('/(\d+)/', $request->catalogue_name, $matches) 
                ? (int) $matches[1] 
                : 0;
            
            // Check if package already exists
            $existingPackage = Package::where('game_id', $id)
                ->where('name', $request->catalogue_name)
                ->first();
            
            if ($existingPackage) {
                // Update existing package
                $existingPackage->update([
                    'currency_amount' => $currencyAmount,
                    'price' => $priceInKs,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Package updated successfully',
                ]);
            }
            
            // Create new package
            Package::create([
                'game_id' => $id,
                'name' => $request->catalogue_name,
                'currency_amount' => $currencyAmount,
                'price' => $priceInKs,
                'bonus' => 0,
                'is_active' => true,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Package imported successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get game code mapping for G2Bulk API
     */
    protected function getGameCode(string $gameName): string
    {
        $mapping = [
            'Mobile Legends' => 'mlbb',
            'mobile legends' => 'mlbb',
            'MLBB' => 'mlbb',
            'PUBG Mobile' => 'pubgm',
            'pubg mobile' => 'pubgm',
            'PUBGMobile' => 'pubgm',
            'Free Fire' => 'freefire',
            'free fire' => 'freefire',
            'FreeFire' => 'freefire',
            'Valorant' => 'valorant',
            'valorant' => 'valorant',
            'Honor of Kings' => 'hok',
            'HOK' => 'hok',
            'Arena of Valor' => 'hok',
        ];

        // Try exact match first
        if (isset($mapping[$gameName])) {
            return $mapping[$gameName];
        }

        // Try case-insensitive match
        $lowerName = strtolower($gameName);
        foreach ($mapping as $key => $value) {
            if (strtolower($key) === $lowerName) {
                return $value;
            }
        }

        // Default: convert to lowercase and replace spaces with underscores
        return strtolower(str_replace(' ', '_', $gameName));
    }

    public function storePackage(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'currency_amount' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
            'bonus' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Package::create([
            'game_id' => $id,
            ...$request->all(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Package created successfully',
        ]);
    }

    public function updatePackage(Request $request, $gameId, $packageId)
    {
        $package = Package::where('game_id', $gameId)->findOrFail($packageId);

        $request->validate([
            'name' => 'required|string|max:255',
            'currency_amount' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
            'bonus' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $package->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Package updated successfully',
        ]);
    }

    public function deletePackage($gameId, $packageId)
    {
        $package = Package::where('game_id', $gameId)->findOrFail($packageId);
        $package->delete();

        return response()->json(['success' => true]);
    }
    
    public function getPackage($gameId, $packageId)
    {
        $package = Package::where('game_id', $gameId)->findOrFail($packageId);
        
        return response()->json([
            'success' => true,
            'package' => $package,
        ]);
    }
}

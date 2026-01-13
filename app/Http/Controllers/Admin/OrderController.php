<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Jobs\ProcessOrder;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['game', 'user', 'package'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->game_id) {
            $query->where('game_id', $request->game_id);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_id', 'like', "%{$request->search}%")
                  ->orWhere('user_game_id', 'like', "%{$request->search}%");
            });
        }

        $orders = $query->paginate(20);
        $games = Game::all();

        return view('admin.orders.index', compact('orders', 'games'));
    }

    public function show($id)
    {
        $order = Order::with(['game', 'package', 'user', 'payment'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function retry($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be retried',
            ], 422);
        }

        $order->incrementRetry();
        dispatch(new ProcessOrder($order));

        return response()->json(['success' => true]);
    }

    public function manualComplete($id)
    {
        $order = Order::findOrFail($id);
        $order->markAsCompleted();

        return response()->json(['success' => true]);
    }

    public function refund($id)
    {
        $order = Order::with('user')->findOrFail($id);

        if ($order->status !== 'failed' || !$order->user) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot refund this order',
            ], 422);
        }

        $order->user->addBalance(
            $order->amount,
            "Refund for Order #{$order->order_id}",
            Order::class,
            $order->id
        );

        return response()->json(['success' => true]);
    }
}

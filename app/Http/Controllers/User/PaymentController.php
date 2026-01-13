<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function show($orderId)
    {
        $order = Order::with(['game', 'package', 'payment'])
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        return view('user.payment', compact('order'));
    }

    public function process(Request $request, $orderId)
    {
        $order = Order::with('payment')
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        $request->validate([
            'payment_method' => 'required|in:wallet,wavepay,kpay,manual',
            'screenshot' => 'nullable|image|max:5120', // 5MB
        ]);

        // Update payment method if changed
        if ($order->payment) {
            $order->payment->update([
                'method' => $request->payment_method,
            ]);
        } else {
            $order->payment()->create([
                'user_id' => auth()->id(),
                'method' => $request->payment_method,
                'amount' => $order->amount,
                'status' => 'pending',
            ]);
        }

        // Handle screenshot upload for manual payment
        if ($request->payment_method === 'manual' && $request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store('payments', 'public');
            $order->payment->update(['screenshot' => $path]);
        }

        // Handle wallet payment
        if ($request->payment_method === 'wallet') {
            if (!auth()->user()->hasEnoughBalance($order->amount)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient wallet balance',
                ], 422);
            }

            auth()->user()->deductBalance(
                $order->amount,
                "Order #{$order->order_id}",
                Order::class,
                $order->id
            );

            $order->payment->approve();
            
            // Process order
            dispatch(new \App\Jobs\ProcessOrder($order));
        }

        // Handle payment gateway redirects
        if (in_array($request->payment_method, ['wavepay', 'kpay'])) {
            // TODO: Integrate with payment gateways
            return response()->json([
                'success' => true,
                'redirect' => route('payment.gateway', ['order' => $order->id, 'method' => $request->payment_method]),
            ]);
        }

        return response()->json([
            'success' => true,
            'redirect' => route('orders.show', $order->id),
        ]);
    }
}

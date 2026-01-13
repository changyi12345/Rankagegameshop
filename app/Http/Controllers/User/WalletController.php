<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Pre-load banks once to avoid multiple queries in view
        $activeBanks = \App\Models\Bank::active()->ordered()->get();
        $kpayBank = $activeBanks->first(function($bank) {
            return stripos($bank->bank_name, 'KBZ') !== false 
                || stripos($bank->bank_name, 'KPay') !== false;
        });
        
        return view('user.wallet', compact('activeBanks', 'kpayBank'));
    }

    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|in:wavepay,kpay,manual',
            'screenshot' => 'required_if:payment_method,manual|image|max:5120', // 5MB
        ]);

        // Check if payment method is enabled
        $methodEnabled = \App\Models\Setting::get("payment_method_{$request->payment_method}_enabled", false);
        
        if ($request->payment_method !== 'manual' && !$methodEnabled) {
            return response()->json([
                'success' => false,
                'message' => 'This payment method is currently disabled. Please use Manual Bank Transfer.',
            ], 422);
        }

        // Handle KPay payment
        if ($request->payment_method === 'kpay') {
            $request->validate([
                'kpay_account' => 'required|string|max:255',
                'screenshot' => 'required|image|max:5120', // Screenshot required for KPay
            ]);

            $kpayService = new \App\Services\KPayService();
            $result = $kpayService->createPayment(
                auth()->id(),
                $request->amount,
                'Wallet Top-up via KPay'
            );

            $payment = $result['payment'];

            // Store KPay account and screenshot
            $payment->update([
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'kpay_account' => $request->kpay_account,
                ]),
            ]);

            if ($request->hasFile('screenshot')) {
                $path = $request->file('screenshot')->store('payments/kpay', 'public');
                $payment->update(['screenshot' => $path]);
            }

            // Create transaction record
            $user = auth()->user();
            \App\Models\Transaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $request->amount,
                'balance_after' => $user->balance,
                'description' => 'KPay Wallet Top-up (Pending Approval)',
                'reference_id' => $payment->id,
                'reference_type' => \App\Models\Payment::class,
            ]);

            // If payment URL is available, redirect to it
            if ($result['payment_url']) {
                return response()->json([
                    'success' => true,
                    'redirect' => true,
                    'payment_url' => $result['payment_url'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'KPay payment request submitted! Please upload your payment screenshot. Your wallet will be credited within 24 hours after admin approval.',
            ]);
        }

        // Handle WavePay payment
        if ($request->payment_method === 'wavepay') {
            // TODO: Implement WavePay integration
            return response()->json([
                'success' => false,
                'message' => 'WavePay integration is in progress. Please use KPay or Manual Bank Transfer.',
            ], 422);
        }

        // Create payment record for wallet top-up
        $payment = \App\Models\Payment::create([
            'user_id' => auth()->id(),
            'order_id' => null, // Wallet top-up doesn't have an order
            'method' => 'manual',
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        // Handle screenshot upload
        if ($request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store('payments', 'public');
            $payment->update(['screenshot' => $path]);
        }

        // Create transaction record
        $user = auth()->user();
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => $request->amount,
            'balance_after' => $user->balance, // Current balance (not updated yet, pending approval)
            'description' => 'Wallet Top-up (Pending Approval)',
            'reference_id' => $payment->id,
            'reference_type' => \App\Models\Payment::class,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Top-up request submitted successfully! Your payment screenshot has been uploaded. Your wallet will be credited within 24 hours after admin approval.',
        ]);
    }
}

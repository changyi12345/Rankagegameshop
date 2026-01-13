<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KPayService
{
    protected $apiUrl;
    protected $merchantId;
    protected $apiKey;
    protected $isTestMode;

    public function __construct()
    {
        $this->apiUrl = config('services.kpay.api_url', 'https://api.kpay.com.mm');
        $this->merchantId = config('services.kpay.merchant_id');
        $this->apiKey = config('services.kpay.api_key');
        $this->isTestMode = config('services.kpay.test_mode', true);
    }

    /**
     * Create a payment request
     */
    public function createPayment($userId, $amount, $description = 'Wallet Top-up')
    {
        $payment = Payment::create([
            'user_id' => $userId,
            'order_id' => null,
            'method' => 'kpay',
            'amount' => $amount,
            'status' => 'pending',
        ]);

        // If API is configured, make API call
        if ($this->merchantId && $this->apiKey && !$this->isTestMode) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl . '/api/v1/payments', [
                    'merchant_id' => $this->merchantId,
                    'amount' => $amount,
                    'currency' => 'MMK',
                    'description' => $description,
                    'reference_id' => $payment->id,
                    'callback_url' => route('payment.kpay.callback'),
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $payment->update([
                        'transaction_id' => $data['transaction_id'] ?? null,
                        'gateway_response' => $data,
                    ]);

                    return [
                        'success' => true,
                        'payment' => $payment,
                        'payment_url' => $data['payment_url'] ?? null,
                        'qr_code' => $data['qr_code'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                Log::error('KPay API Error: ' . $e->getMessage());
            }
        }

        // Return payment for manual processing
        return [
            'success' => true,
            'payment' => $payment,
            'payment_url' => null,
            'qr_code' => null,
        ];
    }

    /**
     * Verify payment callback
     */
    public function verifyCallback($transactionId, $signature)
    {
        // Verify signature with KPay
        // This is a placeholder - implement actual verification logic
        return true;
    }

    /**
     * Process payment callback
     */
    public function processCallback($data)
    {
        $payment = Payment::where('transaction_id', $data['transaction_id'])
            ->orWhere('id', $data['reference_id'])
            ->first();

        if (!$payment) {
            return false;
        }

        if ($data['status'] === 'success') {
            $payment->approve();
            
            // Credit user wallet
            if ($payment->user && !$payment->order_id) {
                $payment->user->addBalance(
                    $payment->amount,
                    "KPay Wallet Top-up",
                    Payment::class,
                    $payment->id
                );
            }

            return true;
        }

        return false;
    }
}

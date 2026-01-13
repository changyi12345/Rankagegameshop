<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $pending_payments = Payment::with(['order.game', 'user'])
            ->where('status', 'pending')
            ->whereIn('method', ['manual', 'kpay'])
            ->latest()
            ->get();

        return view('admin.payments.index', compact('pending_payments'));
    }

    public function toggleMethod(Request $request)
    {
        $method = $request->method;
        $enabled = $request->enabled;

        \App\Models\Setting::set("payment_method_{$method}_enabled", $enabled, 'boolean');

        return response()->json(['success' => true]);
    }

    public function approve($id)
    {
        try {
            \DB::beginTransaction();
            
            $payment = Payment::with(['order', 'user'])->findOrFail($id);
            
            if ($payment->status !== 'pending') {
                \DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already processed',
                ], 422);
            }

            // Approve payment
            $payment->approve();

            // If this is a wallet top-up (no order_id), credit the user's wallet
            if (!$payment->order_id && $payment->user) {
                // Find existing pending transaction
                $transaction = \App\Models\Transaction::where('reference_type', Payment::class)
                    ->where('reference_id', $payment->id)
                    ->where('type', 'credit')
                    ->first();
                
                $description = $payment->method === 'kpay' ? 'KPay Wallet Top-up Approved' : 'Manual Bank Transfer Wallet Top-up Approved';
                
                if ($transaction) {
                    // Update user balance
                    $oldBalance = $payment->user->balance;
                    $payment->user->balance += $payment->amount;
                    $payment->user->save();
                    
                    // Update existing transaction
                    $transaction->update([
                        'description' => $description,
                        'balance_after' => $payment->user->balance,
                    ]);
                } else {
                    // Create new transaction using addBalance
                    $payment->user->addBalance(
                        $payment->amount,
                        $description,
                        Payment::class,
                        $payment->id
                    );
                }

                // Create notification for user
                $methodName = $payment->method === 'kpay' ? 'KPay' : 'Manual Bank Transfer';
                \App\Models\Notification::create([
                    'type' => 'payment',
                    'recipient_type' => 'user',
                    'recipient_id' => $payment->user->id,
                    'title' => 'Wallet Top-up Approved',
                    'message' => "Your {$methodName} wallet top-up of " . number_format($payment->amount) . " Ks has been approved. Your wallet has been credited.",
                    'status' => 'sent',
                    'is_read' => false,
                    'channel' => 'web',
                    'sent_at' => now(),
                ]);
            } elseif ($payment->order && $payment->user) {
                // Create notification for order payment
                \App\Models\Notification::create([
                    'type' => 'payment',
                    'recipient_type' => 'user',
                    'recipient_id' => $payment->user->id,
                    'title' => 'Payment Approved',
                    'message' => "Your payment of " . number_format($payment->amount) . " Ks for Order #{$payment->order->order_id} has been approved.",
                    'status' => 'sent',
                    'is_read' => false,
                    'channel' => 'web',
                    'sent_at' => now(),
                ]);
            }

            // Process the order if it exists
            if ($payment->order) {
                dispatch(new \App\Jobs\ProcessOrder($payment->order));
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment approved successfully',
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Payment approval failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve payment. Please try again.',
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            
            $request->validate([
                'reason' => 'required|string|max:500',
            ]);
            
            $payment = Payment::with(['user', 'order'])->findOrFail($id);
            
            if ($payment->status !== 'pending') {
                \DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already processed',
                ], 422);
            }
            
            $payment->reject($request->reason);

            // Update transaction description if exists
            $transaction = \App\Models\Transaction::where('reference_type', Payment::class)
                ->where('reference_id', $payment->id)
                ->first();
            
            if ($transaction) {
                $transaction->update([
                    'description' => ($payment->method === 'kpay' ? 'KPay' : 'Manual Bank Transfer') . ' Wallet Top-up Rejected',
                ]);
            }

            // Create notification for user
            if ($payment->user) {
                if ($payment->order_id && $payment->order) {
                    $message = "Your payment of " . number_format($payment->amount) . " Ks for Order #{$payment->order->order_id} has been rejected.";
                } else {
                    $methodName = $payment->method === 'kpay' ? 'KPay' : 'Manual Bank Transfer';
                    $message = "Your {$methodName} wallet top-up of " . number_format($payment->amount) . " Ks has been rejected.";
                }

                if ($request->reason) {
                    $message .= " Reason: " . $request->reason;
                }

                \App\Models\Notification::create([
                    'type' => 'payment',
                    'recipient_type' => 'user',
                    'recipient_id' => $payment->user->id,
                    'title' => 'Payment Rejected',
                    'message' => $message,
                    'status' => 'sent',
                    'is_read' => false,
                    'channel' => 'web',
                    'sent_at' => now(),
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Payment rejection failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject payment. Please try again.',
            ], 500);
        }
    }

    public function updateBankDetails(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);

        \App\Models\Setting::set('bank_name', $request->bank_name);
        \App\Models\Setting::set('bank_account_name', $request->account_name);
        \App\Models\Setting::set('bank_account_number', $request->account_number);

        return response()->json([
            'success' => true,
            'message' => 'Bank details updated successfully',
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ]);
    }
}

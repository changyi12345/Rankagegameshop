<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->botToken = Setting::get('telegram_bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send message to Telegram
     */
    public function sendMessage(string $chatId, string $message, array $options = []): bool
    {
        if (!$this->botToken || !$chatId) {
            return false;
        }

        try {
            $response = Http::post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                ...$options,
            ]);

            $data = $response->json();

            if ($data['ok'] ?? false) {
                return true;
            }

            Log::error('Telegram API Error', [
                'response' => $data,
                'chat_id' => $chatId,
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Telegram Service Error', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
            ]);

            return false;
        }
    }

    /**
     * Notify admin about new order
     */
    public function notifyNewOrder($order): void
    {
        if (!Setting::get('telegram_notify_new_order', true)) {
            return;
        }

        $chatId = Setting::get('telegram_admin_chat_id');
        if (!$chatId) {
            return;
        }

        $message = "🆕 <b>New Order</b>\n\n";
        $message .= "Order ID: <code>{$order->order_id}</code>\n";
        $message .= "Game: {$order->game->name}\n";
        $message .= "Amount: " . number_format($order->amount) . " Ks\n";
        $message .= "User: {$order->user->name} ({$order->user->phone})\n";
        $message .= "Status: {$order->status}";

        $this->sendMessage($chatId, $message);

        // Save notification record
        Notification::create([
            'type' => 'order',
            'recipient_type' => 'admin',
            'title' => 'New Order',
            'message' => $message,
            'status' => 'sent',
            'channel' => 'telegram',
        ]);
    }

    /**
     * Notify admin about pending payment
     */
    public function notifyPaymentPending($payment): void
    {
        if (!Setting::get('telegram_notify_payment_pending', true)) {
            return;
        }

        $chatId = Setting::get('telegram_admin_chat_id');
        if (!$chatId) {
            return;
        }

        $message = "💰 <b>Payment Pending</b>\n\n";
        $message .= "Order ID: <code>{$payment->order->order_id}</code>\n";
        $message .= "Amount: " . number_format($payment->amount) . " Ks\n";
        $message .= "Method: {$payment->method}\n";
        $message .= "User: {$payment->user->name} ({$payment->user->phone})";

        $this->sendMessage($chatId, $message);

        Notification::create([
            'type' => 'payment',
            'recipient_type' => 'admin',
            'title' => 'Payment Pending',
            'message' => $message,
            'status' => 'sent',
            'channel' => 'telegram',
        ]);
    }

    /**
     * Notify admin about low balance
     */
    public function notifyLowBalance(float $balance): void
    {
        if (!Setting::get('telegram_notify_low_balance', true)) {
            return;
        }

        $chatId = Setting::get('telegram_admin_chat_id');
        if (!$chatId) {
            return;
        }

        $threshold = Setting::get('g2bulk_low_balance_threshold', 10);

        if ($balance < $threshold) {
            $message = "⚠️ <b>Low Balance Alert</b>\n\n";
            $message .= "Current Balance: " . number_format($balance) . " Ks\n";
            $message .= "Threshold: " . number_format($threshold) . " Ks";

            $this->sendMessage($chatId, $message);

            Notification::create([
                'type' => 'balance',
                'recipient_type' => 'admin',
                'title' => 'Low Balance Alert',
                'message' => $message,
                'status' => 'sent',
                'channel' => 'telegram',
            ]);
        }
    }

    /**
     * Send order status update to user
     */
    public function sendOrderStatus($order, $user): void
    {
        if (!$user->telegram_id) {
            return;
        }

        $statusEmoji = [
            'completed' => '✅',
            'failed' => '❌',
            'pending' => '⏳',
            'processing' => '🔄',
        ];

        $emoji = $statusEmoji[$order->status] ?? '📦';

        $message = "{$emoji} <b>Order Update</b>\n\n";
        $message .= "Order ID: <code>{$order->order_id}</code>\n";
        $message .= "Status: <b>{$order->status}</b>\n";
        $message .= "Game: {$order->game->name}";

        if ($order->status === 'completed') {
            $message .= "\n\n✅ Your top-up has been completed successfully!";
        } elseif ($order->status === 'failed') {
            $message .= "\n\n❌ Your order failed. Amount will be refunded to your wallet.";
        }

        $this->sendMessage($user->telegram_id, $message);

        Notification::create([
            'type' => 'order_status',
            'recipient_type' => 'user',
            'recipient_id' => $user->id,
            'title' => 'Order Status Update',
            'message' => $message,
            'status' => 'sent',
            'channel' => 'telegram',
        ]);
    }
}

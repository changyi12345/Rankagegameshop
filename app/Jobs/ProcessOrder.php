<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\G2BulkService;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // Retry after 1min, 5min, 15min

    public function __construct(
        public Order $order
    ) {}

    public function handle(): void
    {
        try {
            // Check if order is already processed
            if ($this->order->status === 'completed') {
                return;
            }

            // Update status to processing
            $this->order->update(['status' => 'processing']);

            // Process via G2Bulk API
            $service = new G2BulkService();
            $result = $service->processTopUp($this->order);

            if ($result['success']) {
                // Check if order is completed
                $this->order->refresh();
                if ($this->order->status === 'completed') {
                    // Notify user via Telegram
                    if ($this->order->user && $this->order->user->telegram_id) {
                        $telegram = new TelegramService();
                        $telegram->sendOrderStatus($this->order, $this->order->user);
                    }

                    // Create notification for user
                    \App\Models\Notification::create([
                        'type' => 'order',
                        'recipient_type' => 'user',
                        'recipient_id' => $this->order->user_id,
                        'title' => 'Order Completed',
                        'message' => "Your order #{$this->order->order_id} has been completed successfully!",
                        'status' => 'sent',
                        'is_read' => false,
                        'channel' => 'web',
                        'sent_at' => now(),
                    ]);
                }

                Log::info("Order processed successfully", [
                    'order_id' => $this->order->order_id,
                    'status' => $this->order->status,
                ]);
            } else {
                // Check if we should retry
                $maxRetries = \App\Models\Setting::get('g2bulk_max_retries', 3);
                
                if ($this->order->retry_count < $maxRetries) {
                    $this->order->incrementRetry();
                    $this->release(60); // Retry after 1 minute
                } else {
                    // Refund to wallet if payment was made
                    if ($this->order->payment && $this->order->payment->status === 'approved') {
                        $this->order->user->addBalance(
                            $this->order->amount,
                            "Refund for failed Order #{$this->order->order_id}",
                            Order::class,
                            $this->order->id
                        );
                    }

                    $this->order->markAsFailed($result['message'] ?? 'Processing failed');
                }
            }
        } catch (\Exception $e) {
            Log::error("Order processing error", [
                'order_id' => $this->order->order_id,
                'error' => $e->getMessage(),
            ]);

            $this->order->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->order->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        Log::error("Order processing job failed", [
            'order_id' => $this->order->order_id,
            'error' => $exception->getMessage(),
        ]);
    }
}

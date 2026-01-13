<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\G2BulkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle G2Bulk order status callback
     */
    public function g2bulkCallback(Request $request)
    {
        try {
            $data = $request->all();

            // Log full request for debugging
            Log::info('G2Bulk Webhook Received', [
                'data' => $data,
                'headers' => $request->headers->all(),
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);

            // Validate required fields
            if (!isset($data['order_id']) || !isset($data['status'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields',
                ], 400);
            }

            $g2bulkOrderId = $data['order_id'];
            $status = $data['status'];
            $gameCode = $data['game_code'] ?? null;

            // Find order by G2Bulk order ID (stored in api_response)
            // Or we can store G2Bulk order ID in a separate field
            $order = Order::whereJsonContains('api_response->order->order_id', $g2bulkOrderId)
                ->orWhere('id', $g2bulkOrderId) // Fallback: try direct ID match
                ->first();

            if (!$order) {
                Log::warning('G2Bulk Webhook: Order not found', [
                    'g2bulk_order_id' => $g2bulkOrderId,
                    'data' => $data,
                ]);

                // Return 200 to acknowledge receipt even if order not found
                return response()->json([
                    'success' => true,
                    'message' => 'Webhook received',
                ]);
            }

            // Map G2Bulk status to our order status
            $statusMap = [
                'PENDING' => 'pending',
                'PROCESSING' => 'processing',
                'COMPLETED' => 'completed',
                'FAILED' => 'failed',
            ];

            $newStatus = $statusMap[strtoupper($status)] ?? 'pending';

            // Update order status
            $order->update([
                'status' => $newStatus,
                'api_response' => array_merge($order->api_response ?? [], [
                    'webhook_data' => $data,
                    'webhook_received_at' => now()->toIso8601String(),
                ]),
            ]);

            // If completed, mark as completed
            if ($newStatus === 'completed') {
                $order->markAsCompleted();
            }

            // If failed, mark as failed
            if ($newStatus === 'failed') {
                $order->update([
                    'error_message' => $data['message'] ?? 'Order failed',
                ]);
                $order->markAsFailed($data['message'] ?? 'Order failed');
            }

            Log::info('G2Bulk Webhook: Order updated', [
                'order_id' => $order->order_id,
                'status' => $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated',
            ]);

        } catch (\Exception $e) {
            Log::error('G2Bulk Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
            ]);

            // Return 200 to prevent retries for system errors
            return response()->json([
                'success' => false,
                'message' => 'Error processing webhook: ' . $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Test webhook endpoint (GET request for testing)
     */
    public function testWebhook()
    {
        return response()->json([
            'success' => true,
            'message' => 'Webhook endpoint is working!',
            'timestamp' => now()->toIso8601String(),
            'webhook_url' => url('/webhook/g2bulk'),
            'method' => 'POST',
            'note' => 'Use POST method to send webhook data. This is a test endpoint.',
            'callback_url' => \App\Models\Setting::get('g2bulk_callback_url', 'Not configured'),
        ]);
    }
}

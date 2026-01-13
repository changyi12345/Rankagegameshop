<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\ApiLog;
use App\Models\Order;
use Amk\SmileOne\Facades\SmileOne;
use Illuminate\Support\Facades\Log;

class SmileOneService
{
    protected $region;

    public function __construct()
    {
        // Get region from settings, default to 'br' (Brazil)
        $this->region = Setting::get('smile_one_region', config('smileone.default_region', 'br'));
        
        // Update config dynamically from settings if available
        $this->updateConfigFromSettings();
    }

    /**
     * Update config from settings (for runtime config updates)
     */
    protected function updateConfigFromSettings()
    {
        $uid = Setting::get('smile_one_uid');
        $email = Setting::get('smile_one_email');
        $key = Setting::get('smile_one_key');
        $domain = Setting::get('smile_one_domain');

        if ($uid || $email || $key || $domain) {
            config([
                'smileone.uid' => $uid ?: config('smileone.uid'),
                'smileone.email' => $email ?: config('smileone.email'),
                'smileone.key' => $key ?: config('smileone.key'),
                'smileone.domain' => $domain ?: config('smileone.domain'),
            ]);
        }
    }

    /**
     * Get account balance (SmilePoints)
     */
    public function getBalance(): float
    {
        try {
            // Get product name for balance check (using mobilelegends as default)
            $productName = $this->getProductName('Mobile Legends');
            $response = SmileOne::setProduct($productName)->getPoints($this->region);

            // Convert object to array if needed
            $responseArray = is_object($response) ? json_decode(json_encode($response), true) : $response;

            if (isset($responseArray['data']) && isset($responseArray['data']['points'])) {
                $balance = (float) $responseArray['data']['points'];
                Setting::set('smile_one_balance', $balance, 'number');
                return $balance;
            }

            if (isset($responseArray['points'])) {
                $balance = (float) $responseArray['points'];
                Setting::set('smile_one_balance', $balance, 'number');
                return $balance;
            }

            throw new \Exception($responseArray['message'] ?? $responseArray['msg'] ?? 'Failed to get balance');
        } catch (\Exception $e) {
            $this->logError('getBalance', $e);
            throw $e;
        }
    }

    /**
     * Get product list for a game
     */
    public function getProductList(string $gameName, string $region = null): array
    {
        try {
            $productName = $this->getProductName($gameName);
            $region = $region ?? $this->region;
            
            $response = SmileOne::setProduct($productName)->getProductList($region);
            
            return [
                'success' => true,
                'data' => $response['data'] ?? [],
            ];
        } catch (\Exception $e) {
            $this->logError('getProductList', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process top-up order
     */
    public function processTopUp(Order $order): array
    {
        try {
            $game = $order->game;
            $package = $order->package;

            if (!$game || !$package) {
                throw new \Exception('Game or package not found');
            }

            // Get product name and ID
            $productName = $this->getProductName($game->name);
            $productId = $package->currency_amount; // Use currency_amount as product ID

            // Get Game ID and Zone ID
            // For Mobile Legends: user_game_id = Game ID, server_id = Zone ID
            // For other games: user_game_id = User ID, server_id = Server ID
            $gameId = $order->user_game_id; // Game ID (MLBB) or User ID (other games)
            $zoneId = $order->server_id; // Zone ID (MLBB) or Server ID (other games)

            if (!$gameId) {
                $gameName = $game->name ?? 'Game';
                if (stripos($gameName, 'Mobile Legends') !== false || stripos($gameName, 'MLBB') !== false) {
                    throw new \Exception('Game ID is required for Mobile Legends');
                } else {
                    throw new \Exception('User game ID is required');
                }
            }

            // For Mobile Legends, Zone ID is required
            if ((stripos($game->name, 'Mobile Legends') !== false || stripos($game->name, 'MLBB') !== false) && !$zoneId) {
                throw new \Exception('Zone ID is required for Mobile Legends');
            }

            // Default zone ID to '0' if not provided (for games that don't require it)
            if (!$zoneId) {
                $zoneId = '0';
            }

            // Log the purchase attempt
            ApiLog::create([
                'api_name' => 'smile_one',
                'endpoint' => 'purchase',
                'method' => 'POST',
                'request_data' => [
                    'product' => $productName,
                    'product_id' => $productId,
                    'game_id' => $gameId, // Game ID for MLBB, User ID for others
                    'zone_id' => $zoneId, // Zone ID for MLBB, Server ID for others
                    'region' => $this->region,
                    'order_id' => $order->order_id,
                ],
                'order_id' => $order->id,
            ]);

            // Make purchase using Smile One library
            // setUser() expects: setUser(userId, zoneId)
            // For MLBB: userId = Game ID, zoneId = Zone ID
            // For other games: userId = User ID, zoneId = Server ID
            $response = SmileOne::setProduct($productName, (string)$productId)
                ->setUser($gameId, $zoneId)
                ->purchase($this->region);

            // Convert object to array if needed
            $responseArray = is_object($response) ? json_decode(json_encode($response), true) : $response;

            // Log response
            ApiLog::create([
                'api_name' => 'smile_one',
                'endpoint' => 'purchase',
                'method' => 'POST',
                'response_data' => $responseArray,
                'status_code' => 200,
                'order_id' => $order->id,
            ]);

            // Check if purchase was successful
            // Smile One typically returns code: 200 for success
            if (isset($responseArray['code']) && $responseArray['code'] == 200) {
                $order->update([
                    'status' => 'completed',
                    'api_response' => $responseArray,
                    'processed_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Top-up completed successfully',
                    'data' => $responseArray['data'] ?? $responseArray,
                ];
            }

            // Handle success status
            if (isset($responseArray['status']) && $responseArray['status'] === 'success') {
                $order->update([
                    'status' => 'completed',
                    'api_response' => $responseArray,
                    'processed_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Top-up completed successfully',
                    'data' => $responseArray['data'] ?? [],
                ];
            }

            // If failed, extract error message
            $errorMessage = $responseArray['message'] ?? $responseArray['msg'] ?? 'Top-up failed';
            throw new \Exception($errorMessage);

        } catch (\Exception $e) {
            $order->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->logError('processTopUp', $e, $order);
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check order status
     */
    public function checkOrderStatus(string $orderId): array
    {
        try {
            // Note: Smile One library doesn't have a direct order status check
            // This would need to be implemented based on their API
            return [
                'success' => false,
                'message' => 'Order status check not available in current library version',
            ];
        } catch (\Exception $e) {
            $this->logError('checkOrderStatus', $e);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get product name mapping for Smile One
     */
    protected function getProductName(string $gameName): string
    {
        $mapping = [
            'Mobile Legends' => 'mobilelegends',
            'mobile legends' => 'mobilelegends',
            'MobileLegends' => 'mobilelegends',
            'PUBG Mobile' => 'pubgmobile',
            'pubg mobile' => 'pubgmobile',
            'PUBGMobile' => 'pubgmobile',
            'Free Fire' => 'freefire',
            'free fire' => 'freefire',
            'FreeFire' => 'freefire',
            'Valorant' => 'valorant',
            'valorant' => 'valorant',
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

        // Default: convert to lowercase and remove spaces
        return strtolower(str_replace(' ', '', $gameName));
    }

    /**
     * Log error
     */
    protected function logError(string $method, \Exception $e, ?Order $order = null): void
    {
        ApiLog::create([
            'api_name' => 'smile_one',
            'endpoint' => $method,
            'method' => 'POST',
            'error_type' => get_class($e),
            'error_message' => $e->getMessage(),
            'order_id' => $order?->id,
        ]);

        Log::error("SmileOne API Error: {$method}", [
            'error' => $e->getMessage(),
            'order_id' => $order?->id,
            'trace' => $e->getTraceAsString(),
        ]);
    }
}

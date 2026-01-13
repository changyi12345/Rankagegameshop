<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\ApiLog;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class G2BulkService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = Setting::get('g2bulk_api_key');
        $this->baseUrl = Setting::get('g2bulk_api_url', 'https://api.g2bulk.com/v1');
        
        // Validate API key
        if (empty($this->apiKey) || trim($this->apiKey) === '') {
            throw new \Exception('G2Bulk API key is not configured. Please set it in Admin Panel > API Settings.');
        }
        
        // Trim whitespace
        $this->apiKey = trim($this->apiKey);
    }

    /**
     * Get account balance
     */
    public function getBalance(): float
    {
        try {
            // Refresh API key from database (in case it was just updated)
            $apiKey = Setting::get('g2bulk_api_key');
            
            // Ensure API key is set
            if (empty($apiKey) || trim($apiKey) === '') {
                throw new \Exception('G2Bulk API key is not configured. Please set it in Admin Panel > API Settings.');
            }

            $apiKey = trim($apiKey);

            // Log the request for debugging
            Log::info('G2Bulk API Request', [
                'url' => "{$this->baseUrl}/getMe",
                'api_key_length' => strlen($apiKey),
                'api_key_preview' => substr($apiKey, 0, 10) . '...',
            ]);

            // GET /v1/getMe - Retrieve authenticated user details including balance
            $response = Http::timeout(10)->withHeaders([
                'X-API-Key' => $apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/getMe");

            // Log response status
            Log::info('G2Bulk API Response', [
                'status' => $response->status(),
                'success' => $response->successful(),
            ]);

            $data = $response->json();

            // Log response for debugging
            if ($response->failed()) {
                Log::error('G2Bulk API Error', [
                    'status' => $response->status(),
                    'response' => $data,
                    'api_key_length' => strlen($apiKey),
                ]);
            }

            if ($data['success'] ?? false) {
                $balance = (float) ($data['balance'] ?? 0);
                Setting::set('g2bulk_balance', $balance, 'number');
                return $balance;
            }

            $errorMessage = $data['message'] ?? $data['detail']['message'] ?? 'Failed to get balance';
            throw new \Exception($errorMessage);
        } catch (\Exception $e) {
            $this->logError('getBalance', $e);
            throw $e;
        }
    }

    /**
     * Get available games
     * GET /v1/games - Public endpoint
     */
    public function getGames(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/games");

            $data = $response->json();

            if ($data['success'] ?? false) {
                return [
                    'success' => true,
                    'games' => $data['games'] ?? [],
                ];
            }

            throw new \Exception($data['message'] ?? 'Failed to get games');
        } catch (\Exception $e) {
            $this->logError('getGames', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get game catalogue (packages/denominations)
     * GET /v1/games/:code/catalogue - Public endpoint
     */
    public function getGameCatalogue(string $gameCode): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/games/{$gameCode}/catalogue");

            $data = $response->json();

            if ($data['success'] ?? false) {
                return [
                    'success' => true,
                    'game' => $data['game'] ?? [],
                    'catalogues' => $data['catalogues'] ?? [],
                ];
            }

            throw new \Exception($data['message'] ?? 'Failed to get game catalogue');
        } catch (\Exception $e) {
            $this->logError('getGameCatalogue', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get available servers for a game
     * POST /v1/games/servers - Public endpoint
     */
    public function getGameServers(string $gameCode): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/games/servers", [
                'game' => $gameCode,
            ]);

            $data = $response->json();

            // Check if servers are available (200) or not required (403)
            if (isset($data['code']) && $data['code'] == '200') {
                return [
                    'success' => true,
                    'servers' => $data['servers'] ?? [],
                    'has_servers' => true,
                ];
            }

            // 403 means servers are not required (this is OK)
            if ($response->status() == 403 || (isset($data['detail']['code']) && $data['detail']['code'] == '403')) {
                return [
                    'success' => true,
                    'servers' => [],
                    'has_servers' => false,
                    'message' => $data['detail']['message'] ?? 'Game does not require servers',
                ];
            }

            throw new \Exception($data['detail']['message'] ?? $data['message'] ?? 'Failed to get game servers');
        } catch (\Exception $e) {
            $this->logError('getGameServers', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'servers' => [],
                'has_servers' => false,
            ];
        }
    }

    /**
     * Get required fields for a game
     */
    public function getGameFields(string $gameCode): array
    {
        try {
            // POST /v1/games/fields - Public endpoint
            $response = Http::timeout(10)->post("{$this->baseUrl}/games/fields", [
                'game' => $gameCode,
            ]);

            $data = $response->json();

            if (isset($data['code']) && $data['code'] == '200') {
                return [
                    'success' => true,
                    'fields' => $data['info']['fields'] ?? [],
                    'notes' => $data['info']['notes'] ?? '',
                ];
            }

            throw new \Exception($data['detail']['message'] ?? 'Failed to get game fields');
        } catch (\Exception $e) {
            $this->logError('getGameFields', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check player ID validity
     */
    public function checkPlayerId(string $gameCode, string $playerId, ?string $serverId = null): array
    {
        try {
            // Refresh API key from database (in case it was just updated)
            $apiKey = Setting::get('g2bulk_api_key');
            
            // Ensure API key is set
            if (empty($apiKey) || trim($apiKey) === '') {
                throw new \Exception('G2Bulk API key is not configured');
            }

            $apiKey = trim($apiKey);

            $body = [
                'game' => $gameCode,
                'user_id' => $playerId,
            ];

            if ($serverId) {
                $body['server_id'] = $serverId;
            }

            // POST /v1/games/checkPlayerId - Public endpoint
            $response = Http::timeout(10)->withHeaders([
                'X-API-Key' => $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/games/checkPlayerId", $body);

            $data = $response->json();

            if (isset($data['valid']) && $data['valid'] === 'valid') {
                return [
                    'success' => true,
                    'valid' => true,
                    'player_name' => $data['name'] ?? '',
                    'openid' => $data['openid'] ?? '',
                ];
            }

            return [
                'success' => false,
                'valid' => false,
                'message' => $data['message'] ?? 'Invalid player ID',
            ];
        } catch (\Exception $e) {
            $this->logError('checkPlayerId', $e);
            return [
                'success' => false,
                'valid' => false,
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

            if (!$game) {
                throw new \Exception('Game not found');
            }

            // Get game code
            $gameCode = $this->getGameCode($game->name);

            // Check if order has G2Bulk catalogue data (from API response)
            $apiResponse = $order->api_response ?? [];
            $g2bulkCatalogue = $apiResponse['g2bulk_catalogue'] ?? null;
            
            if ($g2bulkCatalogue) {
                // Use G2Bulk catalogue name directly
                $catalogueName = $g2bulkCatalogue['name'] ?? '';
            } else {
                // Fallback to local package
                $package = $order->package;
                if (!$package) {
                    throw new \Exception('Package not found');
                }
                
                // Get catalogue name (package name)
                // G2Bulk expects catalogue name to match their system exactly
                // Format: "310 Diamonds" or "60 UC" etc.
                $catalogueName = $package->name;
                
                // For Mobile Legends, format might be "310 Diamonds" or just "310"
                // Try to match G2Bulk format: currency_amount + currency_name
                if (stripos($game->name, 'Mobile Legends') !== false || stripos($game->name, 'MLBB') !== false) {
                    // Format: "310 Diamonds" (currency_amount + currency_name)
                    $catalogueName = $package->currency_amount . ' ' . ($game->currency_name ?? 'Diamonds');
                } elseif (stripos($game->name, 'PUBG') !== false) {
                    // Format: "60 UC" for PUBG Mobile
                    $catalogueName = $package->currency_amount . ' UC';
                } else {
                    // Use package name as is
                    $catalogueName = $package->name;
                }
            }

            // Get player ID and server ID
            $playerId = $order->user_game_id;
            $serverId = $order->server_id;

            if (!$playerId) {
                throw new \Exception('Player ID is required');
            }

            // Log the purchase attempt
            ApiLog::create([
                'api_name' => 'g2bulk',
                'endpoint' => 'games/order',
                'method' => 'POST',
                'request_data' => [
                    'game_code' => $gameCode,
                    'catalogue_name' => $catalogueName,
                    'player_id' => $playerId,
                    'server_id' => $serverId,
                    'order_id' => $order->order_id,
                ],
                'order_id' => $order->id,
            ]);

            // Prepare request body
            $requestBody = [
                'catalogue_name' => $catalogueName,
                'player_id' => $playerId,
            ];

            if ($serverId) {
                $requestBody['server_id'] = $serverId;
            }

            // Add callback URL if configured
            $callbackUrl = Setting::get('g2bulk_callback_url');
            if ($callbackUrl) {
                $requestBody['callback_url'] = $callbackUrl;
            }

            // Refresh API key from database (in case it was just updated)
            $apiKey = Setting::get('g2bulk_api_key');
            
            // Ensure API key is set
            if (empty($apiKey) || trim($apiKey) === '') {
                throw new \Exception('G2Bulk API key is not configured. Please set it in Admin Panel > API Settings.');
            }

            $apiKey = trim($apiKey);

            // Make purchase request
            // POST /v1/games/:code/order
            $response = Http::timeout(10)->withHeaders([
                'X-API-Key' => $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/games/{$gameCode}/order", $requestBody);

            $responseData = $response->json();

            // Log response
            ApiLog::create([
                'api_name' => 'g2bulk',
                'endpoint' => 'games/order',
                'method' => 'POST',
                'response_data' => $responseData,
                'status_code' => $response->status(),
                'order_id' => $order->id,
            ]);

            // Check if purchase was successful
            if ($response->successful() && ($responseData['success'] ?? false)) {
                $orderData = $responseData['order'] ?? [];
                
                $order->update([
                    'status' => $this->mapOrderStatus($orderData['status'] ?? 'PENDING'),
                    'api_response' => $responseData,
                    'processed_at' => now(),
                ]);

                // If order is completed, mark as completed
                if (($orderData['status'] ?? '') === 'COMPLETED') {
                    $order->markAsCompleted();
                }

                return [
                    'success' => true,
                    'message' => $responseData['message'] ?? 'Top-up order created successfully',
                    'data' => $orderData,
                ];
            }

            // Handle error response
            $errorMessage = $responseData['message'] ?? 'Top-up failed';
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
    public function checkOrderStatus(string $orderId, string $gameCode): array
    {
        try {
            // Refresh API key from database (in case it was just updated)
            $apiKey = Setting::get('g2bulk_api_key');
            
            // Ensure API key is set
            if (empty($apiKey) || trim($apiKey) === '') {
                throw new \Exception('G2Bulk API key is not configured. Please set it in Admin Panel > API Settings.');
            }

            $apiKey = trim($apiKey);

            // POST /v1/games/order/status - Authentication Required
            $response = Http::timeout(10)->withHeaders([
                'X-API-Key' => $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/games/order/status", [
                'order_id' => $orderId,
                'game' => $gameCode,
            ]);

            $data = $response->json();

            return [
                'success' => $response->successful() && ($data['success'] ?? false),
                'data' => $data,
            ];
        } catch (\Exception $e) {
            $this->logError('checkOrderStatus', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
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
            'PUBG' => 'pubgm',
            'pubg' => 'pubgm',
            'Honor of Kings' => 'hok',
            'honor of kings' => 'hok',
            'HOK' => 'hok',
            'hok' => 'hok',
            'Arena of Valor' => 'hok',
            'arena of valor' => 'hok',
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

        // Default: convert to lowercase and replace spaces with underscores
        return strtolower(str_replace(' ', '_', $gameName));
    }

    /**
     * Map G2Bulk order status to our order status
     */
    protected function mapOrderStatus(string $g2bulkStatus): string
    {
        $mapping = [
            'PENDING' => 'pending',
            'PROCESSING' => 'processing',
            'COMPLETED' => 'completed',
            'FAILED' => 'failed',
        ];

        return $mapping[strtoupper($g2bulkStatus)] ?? 'pending';
    }

    /**
     * Log error
     */
    protected function logError(string $method, \Exception $e, ?Order $order = null): void
    {
        ApiLog::create([
            'api_name' => 'g2bulk',
            'endpoint' => $method,
            'method' => 'POST',
            'error_type' => get_class($e),
            'error_message' => $e->getMessage(),
            'order_id' => $order?->id,
        ]);

        Log::error("G2Bulk API Error: {$method}", [
            'error' => $e->getMessage(),
            'order_id' => $order?->id,
            'trace' => $e->getTraceAsString(),
        ]);
    }

    /**
     * Get all product categories
     * GET /v1/category - Public endpoint
     */
    public function getCategories(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/category");

            $data = $response->json();

            if ($data['success'] ?? false) {
                return [
                    'success' => true,
                    'categories' => $data['categories'] ?? [],
                ];
            }

            throw new \Exception($data['message'] ?? 'Failed to get categories');
        } catch (\Exception $e) {
            $this->logError('getCategories', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'categories' => [],
            ];
        }
    }

    /**
     * Get all products
     * GET /v1/products - Public endpoint
     */
    public function getProducts(?int $categoryId = null): array
    {
        try {
            $url = "{$this->baseUrl}/products";
            if ($categoryId) {
                $url = "{$this->baseUrl}/category/{$categoryId}";
            }

            $response = Http::timeout(10)->get($url);

            $data = $response->json();

            if ($data['success'] ?? false) {
                return [
                    'success' => true,
                    'products' => $data['products'] ?? [],
                ];
            }

            throw new \Exception($data['message'] ?? 'Failed to get products');
        } catch (\Exception $e) {
            $this->logError('getProducts', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'products' => [],
            ];
        }
    }

    /**
     * Get specific product details
     * GET /v1/products/:id - Public endpoint
     */
    public function getProduct(int $productId): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/products/{$productId}");

            $data = $response->json();

            if ($data['success'] ?? false) {
                return [
                    'success' => true,
                    'product' => $data['product'] ?? $data,
                ];
            }

            throw new \Exception($data['message'] ?? 'Failed to get product');
        } catch (\Exception $e) {
            $this->logError('getProduct', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Purchase a product
     * POST /v1/products/:id/purchase - Authentication Required
     */
    public function purchaseProduct(int $productId, int $quantity = 1): array
    {
        try {
            // Refresh API key from database
            $apiKey = Setting::get('g2bulk_api_key');
            
            if (empty($apiKey) || trim($apiKey) === '') {
                throw new \Exception('G2Bulk API key is not configured');
            }

            $apiKey = trim($apiKey);

            $response = Http::timeout(10)->withHeaders([
                'X-API-Key' => $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/products/{$productId}/purchase", [
                'quantity' => $quantity,
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return [
                    'success' => true,
                    'order_id' => $data['order_id'] ?? null,
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'product_id' => $data['product_id'] ?? $productId,
                    'product_title' => $data['product_title'] ?? '',
                    'delivery_items' => $data['delivery_items'] ?? [],
                ];
            }

            throw new \Exception($data['message'] ?? 'Product purchase failed');
        } catch (\Exception $e) {
            $this->logError('purchaseProduct', $e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

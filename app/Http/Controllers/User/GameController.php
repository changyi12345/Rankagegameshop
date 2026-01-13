<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Services\G2BulkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class GameController extends Controller
{
    public function show($id)
    {
        $g2bulkService = new G2BulkService();
        
        // Check if $id is numeric (ID) or string (slug/name)
        if (is_numeric($id)) {
            $game = Game::with('packages')->findOrFail($id);
        } else {
            // Convert slug to name format (e.g., "mobile-legends" -> "Mobile Legends")
            $slug = str_replace('-', ' ', $id);
            $slug = ucwords($slug);
            
            // Common game name mappings
            $gameMappings = [
                'mobile-legends' => 'Mobile Legends',
                'mobile legends' => 'Mobile Legends',
                'pubg' => 'PUBG Mobile',
                'pubg mobile' => 'PUBG Mobile',
                'free-fire' => 'Free Fire',
                'free fire' => 'Free Fire',
                'valorant' => 'Valorant',
            ];
            
            // Check if we have a mapping
            $gameName = $gameMappings[strtolower($id)] ?? $slug;
            
            // Try to find the game
            $game = Game::with('packages')
                ->where('name', $gameName)
                ->orWhereRaw('LOWER(REPLACE(name, " ", "-")) = ?', [strtolower($id)])
                ->orWhereRaw('LOWER(name) = ?', [strtolower($gameName)])
                ->orWhere('name', 'like', '%' . $gameName . '%')
                ->first();
            
            if (!$game) {
                // Last attempt: search by partial match
                $game = Game::with('packages')
                    ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(str_replace('-', ' ', $id)) . '%'])
                    ->first();
            }
            
            if (!$game) {
                abort(404, 'Game not found');
            }
        }
        
        // Get game code for G2Bulk API
        $gameCode = $this->getGameCode($game->name);
        
        // Fetch catalogue from G2Bulk API
        $catalogueResult = $g2bulkService->getGameCatalogue($gameCode);
        $g2bulkCatalogues = $catalogueResult['success'] ? ($catalogueResult['catalogues'] ?? []) : [];
        
        // Get required fields from G2Bulk API
        $fieldsResult = $g2bulkService->getGameFields($gameCode);
        $requiredFields = $fieldsResult['success'] ? ($fieldsResult['fields'] ?? []) : [];
        $fieldNotes = $fieldsResult['notes'] ?? '';
        
        // Get available servers from G2Bulk API
        $serversResult = $g2bulkService->getGameServers($gameCode);
        $availableServers = $serversResult['success'] && $serversResult['has_servers'] ? ($serversResult['servers'] ?? []) : [];
        
        // Get local packages (fallback)
        $packages = $game->packages()->active()->orderBy('sort_order')->get();
        
        // Merge G2Bulk catalogues with local packages
        // Convert G2Bulk catalogues to package format
        $g2bulkPackages = [];
        foreach ($g2bulkCatalogues as $catalogue) {
            $g2bulkPackages[] = [
                'id' => ($catalogue['id'] ?? uniqid()),
                'name' => $catalogue['name'] ?? '',
                'price' => ($catalogue['amount'] ?? 0) * (\App\Models\Setting::get('usd_to_kyat_rate', 2100)),
                'currency_amount' => $catalogue['name'] ?? '',
                'bonus' => 0,
                'is_g2bulk' => true,
                'g2bulk_data' => $catalogue,
            ];
        }

        return view('user.games.show', compact('game', 'packages', 'g2bulkPackages', 'requiredFields', 'fieldNotes', 'availableServers', 'gameCode'));
    }

    /**
     * Check player ID via G2Bulk API
     */
    public function checkPlayerId(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'user_id' => 'required|string|min:5',
            'server_id' => 'required|string|min:2',
        ]);

        try {
            $game = Game::findOrFail($request->game_id);
            
            // Get game code for G2Bulk API
            $gameCode = $this->getGameCode($game->name);
            
            $service = new G2BulkService();
            
            // Set timeout for API call (5 seconds)
            $result = $service->checkPlayerId(
                $gameCode,
                $request->user_id,
                $request->server_id
            );

            if ($result['success'] && $result['valid']) {
                return response()->json([
                    'success' => true,
                    'valid' => true,
                    'player_name' => $result['player_name'] ?? '',
                    'openid' => $result['openid'] ?? '',
                ]);
            }

            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => $result['message'] ?? 'Invalid player ID',
            ], 400);
} catch (\Exception $e) {
   Log::error('Player ID check failed', [
        'error' => $e->getMessage(),
        'game_id' => $request->game_id,
        'user_id' => $request->user_id,
    ]);
            
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Failed to verify player ID. Please try again.',
            ], 500);
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
}

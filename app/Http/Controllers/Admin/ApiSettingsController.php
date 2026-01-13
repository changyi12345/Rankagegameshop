<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use App\Services\G2BulkService;

class ApiSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'api_key' => Setting::get('g2bulk_api_key'),
            'api_url' => Setting::get('g2bulk_api_url', 'https://api.g2bulk.com/v1'),
            'callback_url' => Setting::get('g2bulk_callback_url'),
            'auto_retry' => Setting::get('g2bulk_auto_retry', false),
            'max_retries' => Setting::get('g2bulk_max_retries', 3),
            'usd_to_kyat_rate' => Setting::get('usd_to_kyat_rate', 2100),
        ];

        $error_logs = ApiLog::whereNotNull('error_message')
            ->where('api_name', 'g2bulk')
            ->latest()
            ->take(50)
            ->get();

        return view('admin.api-settings.index', compact('settings', 'error_logs'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'api_url' => 'required|url',
            'callback_url' => 'nullable|url',
            'auto_retry' => 'boolean',
            'max_retries' => 'required|integer|min:1|max:10',
            'usd_to_kyat_rate' => 'required|numeric|min:1|max:10000',
        ]);

        Setting::set('g2bulk_api_key', $request->api_key);
        Setting::set('g2bulk_api_url', $request->api_url);
        Setting::set('g2bulk_callback_url', $request->callback_url);
        Setting::set('g2bulk_auto_retry', $request->auto_retry ?? false, 'boolean');
        Setting::set('g2bulk_max_retries', $request->max_retries, 'number');
        Setting::set('usd_to_kyat_rate', $request->usd_to_kyat_rate, 'number');

        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully',
        ]);
    }

    public function testConnection()
    {
        try {
            $apiKey = Setting::get('g2bulk_api_key');
            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please configure G2Bulk API key first',
                ], 400);
            }

            $service = new G2BulkService();
            $balance = $service->getBalance();

            return response()->json([
                'success' => true,
                'message' => 'API connection successful!',
                'balance' => $balance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkBalance()
    {
        try {
            // Check if API key is set
            $apiKey = Setting::get('g2bulk_api_key');
            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please configure G2Bulk API key first',
                ], 400);
            }

            $service = new G2BulkService();
            $balance = $service->getBalance();

            Setting::set('g2bulk_balance', $balance, 'number');

            return response()->json([
                'success' => true,
                'balance' => $balance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

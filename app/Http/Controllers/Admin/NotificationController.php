<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TelegramService;

class NotificationController extends Controller
{
    public function index()
    {
        $bot_settings = [
            'token' => Setting::get('telegram_bot_token'),
            'admin_chat_id' => Setting::get('telegram_admin_chat_id'),
            'notify_new_order' => Setting::get('telegram_notify_new_order', true),
            'notify_payment_pending' => Setting::get('telegram_notify_payment_pending', true),
            'notify_low_balance' => Setting::get('telegram_notify_low_balance', true),
        ];

        $notifications = Notification::latest()->take(50)->get();

        return view('admin.notifications.index', compact('bot_settings', 'notifications'));
    }

    public function saveBot(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'admin_chat_id' => 'required|string',
            'notify_new_order' => 'boolean',
            'notify_payment_pending' => 'boolean',
            'notify_low_balance' => 'boolean',
        ]);

        Setting::set('telegram_bot_token', $request->token);
        Setting::set('telegram_admin_chat_id', $request->admin_chat_id);
        Setting::set('telegram_notify_new_order', $request->notify_new_order ?? false, 'boolean');
        Setting::set('telegram_notify_payment_pending', $request->notify_payment_pending ?? false, 'boolean');
        Setting::set('telegram_notify_low_balance', $request->notify_low_balance ?? false, 'boolean');

        return response()->json([
            'success' => true,
            'message' => 'Bot settings saved successfully',
        ]);
    }

    public function checkBotStatus(Request $request)
    {
        try {
            // Use provided token or get from settings
            $token = $request->input('token') ?? Setting::get('telegram_bot_token');
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bot token not provided',
                ], 422);
            }
            
            $service = new TelegramService($token);
            $status = $service->checkBotStatus();

            return response()->json([
                'success' => $status['valid'],
                'message' => $status['message'],
                'bot' => $status['bot'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyChatId()
    {
        try {
            $chatId = Setting::get('telegram_admin_chat_id');
            
            if (!$chatId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin Chat ID not configured',
                ], 422);
            }

            $service = new TelegramService();
            $result = $service->verifyChatId($chatId);

            return response()->json([
                'success' => $result['valid'],
                'message' => $result['message'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function sendTest()
    {
        try {
            $service = new TelegramService();
            $appUrl = config('app.url', 'http://localhost:8000');
            
            $message = "🧪 <b>Test Notification</b>\n\nThis is a test message from RanKage Game Shop bot.";
            
            // Add buttons to test notification
            $buttons = [
                [
                    [
                        'text' => '🛒 Shop Now',
                        'url' => "{$appUrl}/"
                    ],
                    [
                        'text' => '📦 My Orders',
                        'url' => "{$appUrl}/orders"
                    ]
                ],
                [
                    [
                        'text' => '💰 Wallet',
                        'url' => "{$appUrl}/wallet"
                    ],
                    [
                        'text' => '💬 Support',
                        'url' => "{$appUrl}/support"
                    ]
                ]
            ];
            
            $options = [
                'reply_markup' => [
                    'inline_keyboard' => $buttons
                ]
            ];
            
            $service->sendMessage(
                Setting::get('telegram_admin_chat_id'),
                $message,
                $options
            );

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'send_to_all' => 'boolean',
            'include_buttons' => 'boolean',
        ]);

        try {
            $service = new TelegramService();
            $appUrl = config('app.url', 'http://localhost:8000');
            
            // Create buttons if requested
            $options = [];
            if ($request->include_buttons) {
                $buttons = [
                    [
                        [
                            'text' => '🛒 Shop Now',
                            'url' => "{$appUrl}/"
                        ],
                        [
                            'text' => '📦 My Orders',
                            'url' => "{$appUrl}/orders"
                        ]
                    ],
                    [
                        [
                            'text' => '💰 Wallet',
                            'url' => "{$appUrl}/wallet"
                        ],
                        [
                            'text' => '💬 Support',
                            'url' => "{$appUrl}/support"
                        ]
                    ]
                ];
                
                $options['reply_markup'] = [
                    'inline_keyboard' => $buttons
                ];
            }
            
            if ($request->send_to_all) {
                $users = User::whereNotNull('telegram_id')->get();
                foreach ($users as $user) {
                    $service->sendMessage($user->telegram_id, $request->message, $options);
                }
            } else {
                $service->sendMessage(
                    Setting::get('telegram_admin_chat_id'),
                    $request->message,
                    $options
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Broadcast sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

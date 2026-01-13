<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('user.login');
    }

    public function showRegister()
    {
        return view('user.register');
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            Auth::login($user);

            return response()->json([
                'success' => true,
                'redirect' => route('home'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration. Please try again.',
            ], 500);
        }
    }

    public function loginWithEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember ?? false)) {
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'redirect' => route('home'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password',
        ], 422);
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^09[0-9]{9}$/',
        ]);

        $phone = $request->phone;
        $user = User::firstOrCreate(
            ['phone' => $phone],
            ['name' => 'User ' . substr($phone, -4)]
        );

        $otp = $user->generateOTP();

        // TODO: Send OTP via SMS service (Twilio, etc.)
        // For now, we'll return it in response (remove in production)
        
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => $otp, // Remove this in production
        ]);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^09[0-9]{9}$/',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !$user->verifyOTP($request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code',
            ], 422);
        }

        Auth::login($user, $request->remember ?? false);

        return response()->json([
            'success' => true,
            'redirect' => route('home'),
        ]);
    }

    public function telegramLogin(Request $request)
    {
        // Validate Telegram Login Widget data
        $request->validate([
            'id' => 'required|integer',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'photo_url' => 'nullable|url|max:500',
            'hash' => 'required|string',
            'auth_date' => 'required|integer',
        ]);

        // Verify Telegram data authenticity
        if (!$this->verifyTelegramAuth($request->all())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Telegram authentication data',
            ], 422);
        }

        // Check if auth_date is not too old (24 hours)
        $authDate = $request->auth_date;
        if (time() - $authDate > 86400) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram authentication expired. Please try again.',
            ], 422);
        }

        // Build full name
        $fullName = trim($request->first_name . ' ' . ($request->last_name ?? ''));

        // Find or create user
        $user = User::firstOrCreate(
            ['telegram_id' => (string) $request->id],
            [
                'name' => $fullName,
                'telegram_username' => $request->username ?? null,
            ]
        );

        // Update user info if exists
        if (!$user->wasRecentlyCreated) {
            $user->update([
                'name' => $fullName,
                'telegram_username' => $request->username ?? $user->telegram_username,
            ]);
        }

        Auth::login($user, true);

        return response()->json([
            'success' => true,
            'redirect' => route('home'),
        ]);
    }

    /**
     * Verify Telegram Login Widget authentication data
     * Note: Telegram Login Widget uses different hash verification than Telegram Web App
     */
    protected function verifyTelegramAuth(array $data): bool
    {
        $botToken = config('services.telegram.bot_token');
        
        if (!$botToken) {
            // If bot token is not configured, skip verification (for development)
            // In production, this should return false
            \Log::warning('Telegram bot token not configured, skipping verification');
            return true;
        }

        $hash = $data['hash'] ?? '';
        if (empty($hash)) {
            return false;
        }

        // Remove hash from data for verification
        $checkData = $data;
        unset($checkData['hash']);

        // Create data check string (Telegram Login Widget format)
        ksort($checkData);
        $dataCheckString = '';
        foreach ($checkData as $key => $value) {
            if ($value !== null && $value !== '') {
                $dataCheckString .= $key . '=' . $value . "\n";
            }
        }
        $dataCheckString = rtrim($dataCheckString, "\n");

        // Create secret key from bot token
        $secretKey = hash('sha256', $botToken, true);

        // Calculate hash
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($calculatedHash, $hash);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

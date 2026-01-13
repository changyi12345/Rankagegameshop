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
        // TODO: Implement Telegram login verification
        // For now, create/login user based on Telegram data
        
        $request->validate([
            'id' => 'required',
            'first_name' => 'required',
            'username' => 'nullable',
        ]);

        $user = User::firstOrCreate(
            ['telegram_id' => $request->id],
            [
                'name' => $request->first_name,
                'telegram_username' => $request->username ?? null,
            ]
        );

        Auth::login($user);

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

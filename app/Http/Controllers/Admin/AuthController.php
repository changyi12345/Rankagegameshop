<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$field => $login, 'password' => $request->password], $request->remember)) {
            $user = Auth::user();
            
            if (!$user->is_admin) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Admin only.',
                ], 403);
            }

            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'redirect' => route('admin.dashboard'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 422);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

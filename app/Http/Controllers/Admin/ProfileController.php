<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . auth()->id(),
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_avatar' => 'nullable|boolean',
        ]);

        $data = $request->except('avatar', 'remove_avatar');

        // Handle avatar removal
        if ($request->remove_avatar) {
            if (auth()->user()->avatar && file_exists(public_path('storage/' . auth()->user()->avatar))) {
                unlink(public_path('storage/' . auth()->user()->avatar));
            }
            $data['avatar'] = null;
        }
        // Handle avatar upload
        elseif ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if (auth()->user()->avatar && file_exists(public_path('storage/' . auth()->user()->avatar))) {
                unlink(public_path('storage/' . auth()->user()->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = 'avatar_' . auth()->id() . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('storage/avatars'), $avatarName);
            $data['avatar'] = 'avatars/' . $avatarName;
        }

        auth()->user()->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        auth()->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }
}

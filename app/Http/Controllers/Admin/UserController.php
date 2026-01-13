<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('is_admin', false)->withCount('orders');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function adjustBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $user = User::findOrFail($id);
        
        if ($request->amount > 0) {
            $user->addBalance(
                $request->amount,
                "Admin adjustment",
                null,
                null
            );
        } else {
            $user->deductBalance(
                abs($request->amount),
                "Admin adjustment",
                null,
                null
            );
        }

        return response()->json(['success' => true]);
    }

    public function toggleBlock(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_blocked' => $request->block]);

        return response()->json(['success' => true]);
    }
}

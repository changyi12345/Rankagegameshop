<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        return view('user.support');
    }

    public function contact(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Create notification for admin
        Notification::create([
            'type' => 'support',
            'recipient_type' => 'admin',
            'title' => 'Support Request: ' . $request->subject,
            'message' => "From: " . auth()->user()->name . " (" . auth()->user()->phone . ")\n\n" . $request->message,
            'status' => 'pending',
            'channel' => 'telegram',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
        ]);
    }
}

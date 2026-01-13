<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $games = Game::active()->orderBy('sort_order')->get();
        
        $recentOrders = auth()->check() 
            ? auth()->user()->orders()->with('game')->latest()->take(3)->get()
            : collect();

        // Get promotion settings
        $promotion = [
            'enabled' => \App\Models\Setting::get('promotion_enabled', true),
            'icon' => \App\Models\Setting::get('promotion_icon', '🎉'),
            'title' => \App\Models\Setting::get('promotion_title', 'Special Promotion!'),
            'description' => \App\Models\Setting::get('promotion_description', 'Get 10% extra diamonds on Mobile Legends!'),
            'button_text' => \App\Models\Setting::get('promotion_button_text', 'Shop Now'),
            'button_link' => \App\Models\Setting::get('promotion_button_link', '/games'),
        ];

        return view('user.home', compact('games', 'recentOrders', 'promotion'));
    }
}

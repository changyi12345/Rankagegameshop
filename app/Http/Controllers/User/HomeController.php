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

        return view('user.home', compact('games', 'recentOrders'));
    }
}

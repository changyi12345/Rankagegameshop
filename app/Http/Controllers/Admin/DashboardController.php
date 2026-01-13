<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'g2bulk_balance' => \App\Models\Setting::get('g2bulk_balance', 0),
            'user_count' => User::where('is_admin', false)->count(),
        ];

        $recent_orders = Order::with(['game', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recent_orders'));
    }
}

@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today Orders -->
        <div class="card bg-gradient-to-br from-primary/10 to-primary/5 border-primary/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Today Orders</p>
                    <p class="text-3xl font-bold text-light-text">{{ number_format($stats['today_orders'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-primary/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="card bg-gradient-to-br from-secondary/10 to-secondary/5 border-secondary/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Total Revenue</p>
                    <p class="text-3xl font-bold text-secondary">{{ number_format($stats['total_revenue'] ?? 0) }} <span class="text-lg text-gray-400">Ks</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-secondary/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="card bg-gradient-to-br from-yellow-500/10 to-yellow-500/5 border-yellow-500/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Pending Orders</p>
                    <p class="text-3xl font-bold text-yellow-400">{{ number_format($stats['pending_orders'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- G2Bulk Balance -->
        <div class="card bg-gradient-to-br from-blue-500/10 to-blue-500/5 border-blue-500/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">G2Bulk Balance</p>
                    <p class="text-3xl font-bold text-blue-400">{{ number_format($stats['g2bulk_balance'] ?? 0) }} <span class="text-lg text-gray-400">USD</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-light-text">Recent Orders</h2>
                <a href="/admin/orders" class="text-primary text-sm font-semibold hover:underline">View All</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-dark-border">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Order ID</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Game</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Amount</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_orders ?? [] as $order)
                        <tr class="border-b border-dark-border hover:bg-dark-base transition-colors">
                            <td class="py-3 px-4">
                                <a href="/admin/orders/{{ $order->id }}" class="text-primary hover:underline font-semibold">
                                    #{{ $order->order_id }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xl">{{ $order->game->icon ?? '🎮' }}</span>
                                    <span class="text-light-text">{{ $order->game->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-light-text font-semibold">{{ number_format($order->amount) }} Ks</td>
                            <td class="py-3 px-4">
                                @if($order->status === 'completed')
                                    <span class="badge badge-success">Success</span>
                                @elseif($order->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">Failed</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-400 text-sm">{{ $order->created_at->format('M d, h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">No orders found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('admin.games.create') }}" class="flex items-center justify-center space-x-2 btn-primary w-full py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add New Game</span>
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="flex items-center justify-center space-x-2 btn-outline w-full py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>View Pending Orders</span>
                    </a>
                    <a href="{{ route('admin.api-settings.index') }}" class="flex items-center justify-center space-x-2 btn-outline w-full py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>API Settings</span>
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="flex items-center justify-center space-x-2 btn-outline w-full py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Review Payments</span>
                    </a>
                </div>
            </div>

            <!-- User Count -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Users</h2>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-primary/20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <p class="text-4xl font-bold text-primary mb-2">{{ number_format($stats['user_count'] ?? 0) }}</p>
                    <p class="text-gray-400 text-sm mb-4">Total Users</p>
                    <a href="{{ route('admin.users.index') }}" class="btn-outline w-full py-2 text-center text-sm">View All Users</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

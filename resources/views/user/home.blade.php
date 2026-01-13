@extends('layouts.user')

@section('title', 'Home - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- Top Banner (Promotion/Event) -->
    @if($promotion['enabled'] ?? true)
    <div class="mb-6 rounded-2xl overflow-hidden shadow-xl">
        <div class="bg-gradient-to-r from-primary via-primary-light to-secondary p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
            <div class="relative z-10">
                <h2 class="text-2xl font-bold mb-2">{{ $promotion['icon'] ?? '🎉' }} {{ $promotion['title'] ?? 'Special Promotion!' }}</h2>
                <p class="text-white/90 mb-4">{{ $promotion['description'] ?? 'Get 10% extra diamonds on Mobile Legends!' }}</p>
                <a href="{{ $promotion['button_link'] ?? '/games' }}" class="inline-block bg-white text-primary px-6 py-2 rounded-xl font-semibold hover:bg-gray-100 transition-colors">
                    {{ $promotion['button_text'] ?? 'Shop Now' }}
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Wallet Balance Card (If Logged In) -->
    @auth
    <div class="card mb-6 bg-gradient-to-r from-dark-card to-dark-base">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm mb-1">Wallet Balance</p>
                <p class="text-3xl font-bold text-secondary">{{ number_format(auth()->user()->balance ?? 0) }} <span class="text-lg text-gray-400">Ks</span></p>
            </div>
            <a href="/wallet" class="btn-outline text-sm py-2 px-4">
                Top Up
            </a>
        </div>
    </div>
    @endauth

    <!-- Game Categories -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-light-text mb-4 flex items-center">
            <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
            Popular Games
        </h2>
        
        <!-- Games Grid -->
        <div class="grid grid-cols-2 gap-4">
            @forelse($games as $game)
            <a href="/games/{{ $game->id }}" class="card hover:scale-105 transition-transform group cursor-pointer">
                <div class="aspect-square rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center mb-3 relative overflow-hidden">
                    @if($game->image)
                        <img src="{{ asset('storage/' . $game->image) }}" 
                             alt="{{ $game->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-6xl">{{ $game->icon ?? '🎮' }}</span>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h3 class="font-bold text-light-text mb-1">{{ $game->name }}</h3>
                <p class="text-xs text-gray-400">{{ $game->currency_name }}</p>
                <div class="mt-2 flex items-center">
                    <span class="text-xs text-secondary font-semibold">Starting at</span>
                    <span class="text-sm font-bold text-light-text ml-2">{{ number_format($game->min_price ?? 0) }} Ks</span>
                </div>
            </a>
            @empty
            <div class="col-span-2 text-center py-8 text-gray-400">
                <p>No games available at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Auto TopUp Products Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-light-text flex items-center">
                <span class="w-1 h-6 bg-gradient-to-b from-secondary to-primary rounded-full mr-3"></span>
                Auto TopUp Products
            </h2>
            <a href="{{ route('products.index') }}" class="text-primary text-sm font-semibold">View All</a>
        </div>
        <div class="card bg-gradient-to-r from-dark-card via-dark-base to-dark-card">
            <p class="text-gray-400 text-sm mb-4">Instant delivery products with automatic top-up</p>
            <a href="{{ route('products.index') }}" class="btn-primary w-full py-3 text-center block">
                Browse Products
            </a>
        </div>
    </div>

    <!-- Recent Orders (If Logged In) -->
    @auth
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-light-text flex items-center">
                <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
                Recent Orders
            </h2>
            <a href="/orders" class="text-primary text-sm font-semibold">View All</a>
        </div>
        
        <div class="space-y-3">
            @forelse(auth()->user()->orders()->latest()->take(3)->get() ?? [] as $order)
            <a href="/orders/{{ $order->id }}" class="card hover:bg-dark-base transition-colors block">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="text-2xl">{{ $order->game->icon ?? '🎮' }}</span>
                            <div>
                                <h3 class="font-bold text-light-text">{{ $order->game->name ?? 'Unknown Game' }}</h3>
                                <p class="text-xs text-gray-400">Order #{{ $order->order_id }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-sm">
                            <span class="text-gray-400">{{ number_format($order->amount) }} Ks</span>
                            <span class="text-gray-500">•</span>
                            <span class="text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div>
                        @if($order->status === 'completed')
                            <span class="badge badge-success">Success</span>
                        @elseif($order->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-danger">Failed</span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="card text-center py-8">
                <span class="text-4xl mb-3 block">📦</span>
                <p class="text-gray-400 mb-2">No orders yet</p>
                <a href="/games" class="text-primary text-sm font-semibold">Browse Games</a>
            </div>
            @endforelse
        </div>
    </div>
    @endauth
</div>
@endsection

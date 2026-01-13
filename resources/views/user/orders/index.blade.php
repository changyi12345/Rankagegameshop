@extends('layouts.user')

@section('title', 'Order History - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-4xl">
    <h1 class="text-2xl font-bold text-light-text mb-6 flex items-center">
        <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
        Order History
    </h1>

    <!-- Filter Tabs -->
    <div class="flex space-x-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
        <button class="filter-tab active" data-filter="all">All</button>
        <button class="filter-tab" data-filter="completed">Success</button>
        <button class="filter-tab" data-filter="pending">Pending</button>
        <button class="filter-tab" data-filter="failed">Failed</button>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        @forelse($orders ?? [] as $order)
        @php
            $isMLBB = stripos($order->game->name ?? '', 'Mobile Legends') !== false 
                    || stripos($order->game->name ?? '', 'MLBB') !== false
                    || stripos($order->game->name ?? '', 'mobile legend') !== false
                    || ($order->game->id ?? 0) == 1;
        @endphp
        <a href="/orders/{{ $order->id }}" class="card hover:bg-dark-base transition-colors block">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start space-x-4 flex-1 min-w-0">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center flex-shrink-0">
                        <span class="text-3xl">{{ $order->game->icon ?? '🎮' }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-light-text mb-1">{{ $order->game->name ?? 'Unknown Game' }}</h3>
                        <p class="text-xs text-gray-400 mb-1">#{{ $order->order_id }}</p>
                        <div class="text-sm text-gray-400">
                            @if($order->user_game_id)
                                <span>{{ $isMLBB ? 'Game ID' : 'User ID' }}: </span>
                                <span class="text-light-text font-semibold">{{ $order->user_game_id }}</span>
                            @endif
                            @if($order->server_id)
                                <span class="ml-3">{{ $isMLBB ? 'Zone ID' : 'Server' }}: </span>
                                <span class="text-light-text font-semibold">{{ $order->server_id }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xl font-bold text-secondary mb-2">{{ number_format($order->amount) }} K</p>
                    <div class="mb-2">
                        @if($order->status === 'completed')
                            <span class="badge badge-success">Success</span>
                        @elseif($order->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-danger">Failed</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $order->created_at->format('h:i A') }}</p>
                </div>
            </div>
        </a>
        @empty
        <div class="card text-center py-12">
            <span class="text-5xl mb-4 block">📦</span>
            <h3 class="text-xl font-bold text-light-text mb-2">No orders found</h3>
            <p class="text-gray-400 mb-6">Start shopping to see your orders here</p>
            <a href="/" class="btn-primary inline-block px-6 py-3">Browse Games</a>
        </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
.filter-tab {
    @apply px-4 py-2 rounded-xl font-semibold text-sm bg-dark-base text-gray-400 hover:text-light-text transition-colors whitespace-nowrap;
}
.filter-tab.active {
    @apply bg-primary text-white;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const filter = this.dataset.filter;
        // Filter orders
        const orders = document.querySelectorAll('.space-y-4 > a');
        orders.forEach(order => {
            const status = order.querySelector('.badge')?.textContent.trim().toLowerCase();
            const shouldShow = filter === 'all' || 
                             (filter === 'completed' && status === 'success') ||
                             (filter === 'pending' && status === 'pending') ||
                             (filter === 'failed' && status === 'failed');
            
            order.style.display = shouldShow ? 'block' : 'none';
        });
    });
});
</script>
@endpush
@endsection

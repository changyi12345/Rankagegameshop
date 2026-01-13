@props(['order', 'showUser' => false])

<a href="/orders/{{ $order->id ?? '' }}" class="card hover:bg-dark-base transition-colors block">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center space-x-4 flex-1 min-w-0">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center flex-shrink-0">
                <span class="text-3xl">{{ $order->game->icon ?? '🎮' }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-light-text mb-1 truncate">{{ $order->game->name ?? 'Unknown Game' }}</h3>
                <div class="flex items-center space-x-3 text-sm text-gray-400">
                    <span>#{{ $order->order_id ?? 'N/A' }}</span>
                    <span>•</span>
                    <span>{{ $order->created_at->format('M d, Y') ?? 'N/A' }}</span>
                    @if($showUser && $order->user)
                    <span>•</span>
                    <span>{{ $order->user->name ?? 'Guest' }}</span>
                    @endif
                </div>
                <div class="mt-2 text-sm">
                    <span class="text-gray-400">User ID: </span>
                    <span class="text-light-text font-semibold">{{ $order->user_id ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-xl font-bold text-secondary mb-2">{{ number_format($order->amount ?? 0) }} Ks</p>
            <x-status-badge :status="$order->status ?? 'pending'" />
        </div>
    </div>
</a>

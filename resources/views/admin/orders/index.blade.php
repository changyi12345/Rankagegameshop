@extends('layouts.admin')

@section('title', 'Orders Management - Admin Panel')
@section('page-title', 'Orders Management')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       placeholder="Search by Order ID, User ID..." 
                       class="input-field"
                       id="searchInput">
            </div>
            <select class="input-field" id="statusFilter" style="width: auto; min-width: 150px;">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
                <option value="failed">Failed</option>
            </select>
            <select class="input-field" id="gameFilter" style="width: auto; min-width: 150px;">
                <option value="">All Games</option>
                @foreach($games ?? [] as $game)
                <option value="{{ $game->id }}">{{ $game->name }}</option>
                @endforeach
            </select>
            <button class="btn-primary px-6 py-3">Filter</button>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-border">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Order ID</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Game</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">User</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Amount</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Date</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders ?? [] as $order)
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
                        <td class="py-3 px-4">
                            <div>
                                <p class="text-light-text font-semibold">{{ $order->user->name ?? 'Guest' }}</p>
                                <p class="text-gray-400 text-xs">{{ $order->user->phone ?? 'N/A' }}</p>
                                @if($order->user_game_id)
                                <p class="text-gray-400 text-xs mt-1">
                                    @if(stripos($order->game->name ?? '', 'Mobile Legends') !== false || stripos($order->game->name ?? '', 'MLBB') !== false)
                                        Game ID: <span class="text-light-text">{{ $order->user_game_id }}</span>
                                    @else
                                        User ID: <span class="text-light-text">{{ $order->user_game_id }}</span>
                                    @endif
                                </p>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4 text-light-text font-semibold">{{ number_format($order->amount) }} Ks</td>
                        <td class="py-3 px-4">
                            @if($order->status === 'completed')
                                <span class="badge badge-success">Success</span>
                            @elseif($order->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($order->status === 'processing')
                                <span class="badge badge-info">Processing</span>
                            @else
                                <span class="badge badge-danger">Failed</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-400 text-sm">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-2">
                                <a href="/admin/orders/{{ $order->id }}" class="text-primary hover:text-primary-light text-sm">View</a>
                                @if($order->status === 'pending')
                                    <button onclick="retryOrder({{ $order->id }})" class="text-secondary hover:text-secondary-light text-sm">Retry</button>
                                @endif
                                @if($order->status === 'failed')
                                    <button onclick="refundOrder({{ $order->id }})" class="text-red-400 hover:text-red-300 text-sm">Refund</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">No orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex items-center justify-between">
            <p class="text-gray-400 text-sm">Showing {{ ($orders->currentPage() ?? 1) * 10 - 9 }} to {{ ($orders->currentPage() ?? 1) * 10 }} of {{ $orders->total() ?? 0 }} orders</p>
            <div class="flex space-x-2">
                <button class="btn-outline px-4 py-2 text-sm">Previous</button>
                <button class="btn-outline px-4 py-2 text-sm">Next</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function retryOrder(orderId) {
    if (confirm('Retry this order?')) {
        fetch(`/admin/orders/${orderId}/retry`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

function refundOrder(orderId) {
    if (confirm('Refund this order to user wallet?')) {
        fetch(`/admin/orders/${orderId}/refund`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}
</script>
@endpush
@endsection

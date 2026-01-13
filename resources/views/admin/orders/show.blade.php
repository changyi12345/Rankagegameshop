@extends('layouts.admin')

@section('title', 'Order #' . ($order->order_id ?? '') . ' - Admin Panel')
@section('page-title', 'Order Details')

@section('content')
<div class="max-w-6xl space-y-6">
    <!-- Back Button -->
    <a href="/admin/orders" class="inline-flex items-center text-gray-400 hover:text-primary mb-4 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Orders
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Order Information</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Order ID</span>
                        <span class="text-light-text font-bold">#{{ $order->order_id ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Status</span>
                        @if($order->status === 'completed')
                            <span class="badge badge-success">Success</span>
                        @elseif($order->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($order->status === 'processing')
                            <span class="badge badge-info">Processing</span>
                        @else
                            <span class="badge badge-danger">Failed</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Amount</span>
                        <span class="text-secondary text-xl font-bold">{{ number_format($order->amount ?? 0) }} Ks</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Created At</span>
                        <span class="text-light-text">{{ $order->created_at->format('M d, Y h:i A') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Game Details -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Game Details</h2>
                <div class="flex items-center space-x-4 p-4 bg-dark-base rounded-xl">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                        <span class="text-4xl">{{ $order->game->icon ?? '🎮' }}</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-light-text">{{ $order->game->name ?? 'Unknown Game' }}</h3>
                        <p class="text-gray-400">{{ $order->package->name ?? 'Package' }}</p>
                        <div class="mt-2 space-y-1">
                            @if($order->user_game_id)
                            <p class="text-sm">
                                <span class="text-gray-400">
                                    @if(stripos($order->game->name ?? '', 'Mobile Legends') !== false || stripos($order->game->name ?? '', 'MLBB') !== false)
                                        Game ID:
                                    @else
                                        User ID:
                                    @endif
                                </span> 
                                <span class="text-light-text font-semibold">{{ $order->user_game_id }}</span>
                            </p>
                            @endif
                            @if($order->server_id)
                            <p class="text-sm">
                                <span class="text-gray-400">
                                    @if(stripos($order->game->name ?? '', 'Mobile Legends') !== false || stripos($order->game->name ?? '', 'MLBB') !== false)
                                        Zone ID:
                                    @else
                                        Server ID:
                                    @endif
                                </span> 
                                <span class="text-light-text font-semibold">{{ $order->server_id }}</span>
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Payment Details</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Payment Method</span>
                        <span class="text-light-text font-semibold capitalize">{{ $order->payment_method ?? 'N/A' }}</span>
                    </div>
                    @if($order->payment)
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Payment Status</span>
                        @if($order->payment->status === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($order->payment->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </div>
                    @if($order->payment->screenshot)
                    <div class="p-3 bg-dark-base rounded-xl">
                        <p class="text-gray-400 text-sm mb-2">Payment Screenshot</p>
                        <a href="{{ asset('storage/' . $order->payment->screenshot) }}" target="_blank" class="text-primary hover:underline">View Screenshot</a>
                    </div>
                    @endif
                    @endif
                </div>
            </div>

            <!-- G2Bulk API Response -->
            @if($order->api_response)
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">API Response</h2>
                <div class="bg-dark-base rounded-xl p-4">
                    <pre class="text-xs text-gray-400 overflow-x-auto">{{ json_encode($order->api_response, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Actions</h2>
                <div class="space-y-3">
                    @if($order->status === 'pending')
                    <button onclick="retryOrder()" class="btn-primary w-full py-3">Retry Top-up</button>
                    <button onclick="manualComplete()" class="btn-outline w-full py-3">Manual Complete</button>
                    @endif
                    @if($order->status === 'failed')
                    <button onclick="refundOrder()" class="btn-secondary w-full py-3">Refund to Wallet</button>
                    @endif
                    @if($order->payment && $order->payment->status === 'pending')
                    <button onclick="approvePayment()" class="btn-primary w-full py-3">Approve Payment</button>
                    <button onclick="rejectPayment()" class="btn-outline w-full py-3">Reject Payment</button>
                    @endif
                </div>
            </div>

            <!-- User Info -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">User Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Name</p>
                        <p class="text-light-text font-semibold">{{ $order->user->name ?? 'Guest' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Phone</p>
                        <p class="text-light-text">{{ $order->user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Email</p>
                        <p class="text-light-text">{{ $order->user->email ?? 'N/A' }}</p>
                    </div>
                    <a href="/admin/users/{{ $order->user->id ?? '' }}" class="btn-outline w-full py-2 text-sm text-center mt-4">View User Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function retryOrder() {
    if (confirm('Retry this order?')) {
        fetch('/admin/orders/{{ $order->id ?? 0 }}/retry', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

function manualComplete() {
    if (confirm('Manually mark this order as completed?')) {
        fetch('/admin/orders/{{ $order->id ?? 0 }}/manual-complete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

function refundOrder() {
    if (confirm('Refund this order to user wallet?')) {
        fetch('/admin/orders/{{ $order->id ?? 0 }}/refund', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

function approvePayment() {
    if (confirm('Approve this payment?')) {
        fetch('/admin/payments/{{ $order->payment->id ?? 0 }}/approve', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

function rejectPayment() {
    const reason = prompt('Rejection reason:');
    if (reason !== null) {
        fetch('/admin/payments/{{ $order->payment->id ?? 0 }}/reject', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason })
        }).then(() => location.reload());
    }
}
</script>
@endpush
@endsection

@extends('layouts.user')

@section('title', 'Order #' . ($order->order_id ?? '') . ' - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-2xl">
    <!-- Back Button -->
    <a href="/orders" class="inline-flex items-center text-gray-400 hover:text-primary mb-4 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Orders
    </a>

    <!-- Order Status Card -->
    <div class="card mb-6">
        <div class="text-center mb-6">
            @if($order->status === 'completed')
                <div class="w-20 h-20 rounded-full bg-secondary/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-secondary mb-2">Order Completed!</h1>
                <p class="text-gray-400">Your top-up has been successfully processed</p>
            @elseif($order->status === 'pending')
                <div class="w-20 h-20 rounded-full bg-yellow-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-yellow-400 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-yellow-400 mb-2">Processing...</h1>
                <p class="text-gray-400">Your order is being processed. Please wait.</p>
            @else
                <div class="w-20 h-20 rounded-full bg-red-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-red-400 mb-2">Order Failed</h1>
                <p class="text-gray-400">Your order could not be processed</p>
            @endif
        </div>

        <div class="bg-dark-base rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-400">Order ID</span>
                <span class="text-light-text font-bold">#{{ $order->order_id }}</span>
            </div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-400">Status</span>
                @if($order->status === 'completed')
                    <span class="badge badge-success">Success</span>
                @elseif($order->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                @else
                    <span class="badge badge-danger">Failed</span>
                @endif
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-400">Date</span>
                <span class="text-light-text">{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="card mb-6">
        <h2 class="text-xl font-bold text-light-text mb-4">Order Details</h2>
        
        <div class="space-y-4">
            <!-- Game Info -->
            <div x-data="{ showPackageDetails: false }" class="bg-dark-base rounded-xl overflow-hidden">
                <div @click="showPackageDetails = !showPackageDetails" 
                     class="flex items-center space-x-4 p-4 cursor-pointer hover:bg-dark-card transition-colors">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center flex-shrink-0">
                        <span class="text-3xl">{{ $order->game->icon ?? '🎮' }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-light-text">{{ $order->game->name ?? 'Game' }}</h3>
                        <p class="text-gray-400 text-sm truncate">{{ $order->package->name ?? 'Package' }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400 transition-transform" 
                             :class="{ 'rotate-180': showPackageDetails }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Package Details (Expandable) -->
                <div x-show="showPackageDetails" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="border-t border-dark-border p-4 space-y-3">
                    @if($order->package)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Package Name</p>
                            <p class="text-sm font-semibold text-light-text">{{ $order->package->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Currency Amount</p>
                            <p class="text-sm font-semibold text-primary">
                                {{ number_format($order->package->currency_amount ?? 0) }}
                                <span class="text-gray-400 text-xs ml-1">{{ $order->game->currency_name ?? '' }}</span>
                            </p>
                        </div>
                        @if($order->package->bonus > 0)
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Bonus</p>
                            <p class="text-sm font-semibold text-secondary">+{{ number_format($order->package->bonus) }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Package Price</p>
                            <p class="text-sm font-semibold text-secondary">{{ number_format($order->package->price ?? 0) }} Ks</p>
                        </div>
                    </div>
                    @elseif($order->api_response && isset($order->api_response['g2bulk_catalogue']))
                    @php
                        $g2bulkCatalogue = $order->api_response['g2bulk_catalogue'];
                        $exchangeRate = \App\Models\Setting::get('usd_to_kyat_rate', 2100);
                        $priceInKs = ($g2bulkCatalogue['amount'] ?? 0) * $exchangeRate;
                    @endphp
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Package Name</p>
                            <p class="text-sm font-semibold text-light-text">{{ $g2bulkCatalogue['name'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Price (USD)</p>
                            <p class="text-sm font-semibold text-primary">${{ number_format($g2bulkCatalogue['amount'] ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Price (Ks)</p>
                            <p class="text-sm font-semibold text-secondary">{{ number_format($priceInKs) }} Ks</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Source</p>
                            <span class="badge badge-info text-xs">G2Bulk</span>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-gray-400 text-sm">Package details not available</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Game Account Info -->
            <div class="bg-dark-base rounded-xl p-4 space-y-3" x-data="gameAccountData()">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-300">
                        @if(stripos($order->game->name ?? '', 'Mobile Legends') !== false || stripos($order->game->name ?? '', 'MLBB') !== false)
                            Game Account Details
                        @else
                            Game Account Details
                        @endif
                    </h3>
                    @if($order->status === 'pending' || $order->status === 'failed')
                    <button @click="editing = !editing" class="text-primary hover:text-primary-light text-sm font-semibold">
                        <span x-show="!editing">Edit</span>
                        <span x-show="editing">Cancel</span>
                    </button>
                    @endif
                </div>
                
                <div x-show="!editing" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">
                            @if(stripos($order->game->name ?? '', 'Mobile Legends') !== false || stripos($order->game->name ?? '', 'MLBB') !== false)
                                Game ID
                            @else
                                User ID
                            @endif
                        </span>
                        <span class="text-light-text font-semibold">{{ $order->user_game_id ?? 'N/A' }}</span>
                    </div>
                    @if($order->server_id || $order->game->requires_server ?? false)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">
                            @if(stripos($order->game->name ?? '', 'Mobile Legends') !== false || stripos($order->game->name ?? '', 'MLBB') !== false)
                                Zone ID
                            @else
                                Server ID
                            @endif
                        </span>
                        <span class="text-light-text font-semibold">{{ $order->server_id ?? 'N/A' }}</span>
                    </div>
                    @endif
                </div>

                <!-- Edit Form -->
                <form x-show="editing" @submit.prevent="updateAccountInfo" class="space-y-3">
                    @php
                        $isMLBB = stripos($order->game->name ?? '', 'Mobile Legends') !== false || stripos($order->game->name ?? '', 'MLBB') !== false;
                        $userIdLabel = $isMLBB ? 'Game ID' : 'User ID';
                        $userIdPlaceholder = $isMLBB ? 'Enter Game ID' : 'Enter User ID';
                        $serverIdLabel = $isMLBB ? 'Zone ID' : 'Server ID';
                        $serverIdPlaceholder = $isMLBB ? 'Enter Zone ID' : 'Enter Server ID';
                    @endphp
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">
                            {{ $userIdLabel }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               x-model="form.user_game_id" 
                               required 
                               class="input-field text-sm" 
                               placeholder="{{ $userIdPlaceholder }}">
                    </div>
                    
                    @if($order->game->requires_server ?? false)
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">
                            {{ $serverIdLabel }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               x-model="form.server_id" 
                               required 
                               class="input-field text-sm" 
                               placeholder="{{ $serverIdPlaceholder }}">
                    </div>
                    @endif

                    <div x-show="error" class="text-red-400 text-xs" x-text="error"></div>
                    <div x-show="success" class="text-secondary text-xs" x-text="success"></div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" 
                                class="btn-primary flex-1 py-2 text-sm"
                                :disabled="loading">
                            <span x-show="!loading">Save</span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Payment Info -->
            <div class="bg-dark-base rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-400">Payment Method</span>
                    <span class="text-light-text font-semibold capitalize">{{ $order->payment_method ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-dark-border">
                    <span class="text-light-text font-bold">Total Amount</span>
                    <span class="text-secondary text-2xl font-bold">{{ number_format($order->amount) }} Ks</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    @if($order->status === 'pending' && !$order->payment_method)
    <div class="card mb-6">
        <a href="/payment/{{ $order->id }}" class="btn-primary w-full py-4 text-center block">
            Complete Payment
        </a>
    </div>
    @endif

    @if($order->status === 'failed')
    <div class="card mb-6">
        <p class="text-gray-400 text-sm mb-4 text-center">If payment was made, contact support for refund</p>
        <div class="flex gap-3">
            <a href="/" class="btn-outline flex-1 text-center py-3">Shop Again</a>
            <a href="/support" class="btn-primary flex-1 text-center py-3">Contact Support</a>
        </div>
    </div>
    @endif

    <!-- Support -->
    <div class="card text-center">
        <p class="text-gray-400 text-sm mb-2">Need help?</p>
        <a href="/support" class="text-primary hover:underline font-semibold">Contact Support</a>
    </div>
</div>

@push('scripts')
<script>
function gameAccountData() {
    return {
        editing: false,
        loading: false,
        error: '',
        success: '',
        form: {
            user_game_id: '{{ $order->user_game_id ?? '' }}',
            server_id: '{{ $order->server_id ?? '' }}'
        },
        
        async updateAccountInfo() {
            this.loading = true;
            this.error = '';
            this.success = '';
            
            try {
                const res = await fetch('/orders/{{ $order->id }}/update-account', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    this.success = data.message || 'Account information updated successfully';
                    this.editing = false;
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    this.error = data.message || 'Failed to update account information';
                }
            } catch (e) {
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection

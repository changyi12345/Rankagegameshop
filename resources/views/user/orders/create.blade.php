@extends('layouts.user')

@section('title', 'Create Order - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-2xl">
    <!-- Back Button -->
    <a href="/games/{{ $game->id ?? '' }}" class="inline-flex items-center text-gray-400 hover:text-primary mb-4 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Game
    </a>

    <div x-data="orderFormData()">
        <!-- Game Info Card -->
        <div class="card mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                    <span class="text-4xl">{{ $game->icon ?? '🎮' }}</span>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-light-text">{{ $game->name ?? 'Game' }}</h1>
                    <p class="text-gray-400">{{ $package->name ?? 'Package' }}</p>
                    <p class="text-secondary text-xl font-bold mt-2">{{ number_format($package->price ?? 0) }} Ks</p>
                </div>
            </div>
        </div>

        <!-- Order Form -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">Order Details</h2>
            
            <form @submit.prevent="submitOrder">
                <div class="space-y-4">
                    @php
                        $gameName = strtolower(trim($game->name ?? ''));
                        $isMLBB = $gameName === 'mobile legends' 
                                || stripos($gameName, 'mobile legends') !== false 
                                || stripos($gameName, 'mlbb') !== false
                                || stripos($gameName, 'mobile legend') !== false
                                || $game->id == 1; // Fallback: ID 1 is usually Mobile Legends
                    @endphp
                    
                    <!-- Mobile Legends: Game ID and Zone ID side by side -->
                    @if($isMLBB)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Game ID -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Game ID <span class="text-red-400">*</span>
                            </label>
                            <input type="text" 
                                   x-model="form.user_id" 
                                   required 
                                   class="input-field w-full uppercase" 
                                   placeholder="USER ID"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   @input="form.user_id = form.user_id.replace(/[^0-9]/g, '')"
                                   maxlength="20">
                            <p class="text-xs text-gray-500 mt-1">
                                Your Mobile Legends Game ID
                            </p>
                        </div>

                        <!-- Zone ID -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-gray-300">
                                    Zone ID <span class="text-red-400">*</span>
                                </label>
                                <div class="relative group">
                                    <button type="button" class="w-5 h-5 rounded-full bg-primary/20 text-primary flex items-center justify-center hover:bg-primary/30 transition-colors text-xs font-bold">
                                        ?
                                    </button>
                                    <div class="absolute right-0 top-6 w-48 bg-dark-card border border-dark-border rounded-lg p-3 text-xs text-gray-300 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10 shadow-lg">
                                        Your Mobile Legends Zone ID (e.g., 1234). This is your server/region ID.
                                    </div>
                                </div>
                            </div>
                            <input type="text" 
                                   x-model="form.server_id" 
                                   required 
                                   class="input-field w-full uppercase" 
                                   placeholder="ZONE ID"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   @input="form.server_id = form.server_id.replace(/[^0-9]/g, '')"
                                   maxlength="10">
                            <p class="text-xs text-gray-500 mt-1">
                                Your Mobile Legends Zone ID
                            </p>
                        </div>
                    </div>
                    @else
                    <!-- Other Games: User ID and Server ID -->
                    <!-- User ID -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            User ID <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               x-model="form.user_id" 
                               required 
                               class="input-field" 
                               placeholder="Enter your game User ID">
                        <p class="text-xs text-gray-500 mt-1">
                            Your game account ID
                        </p>
                    </div>

                    <!-- Server ID (if required) -->
                    @if($game->requires_server ?? false)
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Server ID <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               x-model="form.server_id" 
                               required 
                               class="input-field" 
                               placeholder="Enter Server ID">
                        <p class="text-xs text-gray-500 mt-1">
                            Your game server ID
                        </p>
                    </div>
                    @endif
                    @endif

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Payment Method <span class="text-red-400">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            @auth
                            @if(auth()->user()->balance >= ($package->price ?? 0))
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="form.payment_method" 
                                       value="wallet" 
                                       class="peer hidden">
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all text-center py-4">
                                    <span class="text-2xl block mb-1">💰</span>
                                    <span class="text-sm font-semibold text-light-text">Wallet</span>
                                    <p class="text-xs text-gray-400 mt-1">{{ number_format(auth()->user()->balance) }} Ks</p>
                                </div>
                            </label>
                            @endif
                            @endauth
                            
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="form.payment_method" 
                                       value="wavepay" 
                                       class="peer hidden">
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all text-center py-4">
                                    <span class="text-2xl block mb-1">📱</span>
                                    <span class="text-sm font-semibold text-light-text">WavePay</span>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="form.payment_method" 
                                       value="kpay" 
                                       class="peer hidden">
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all text-center py-4">
                                    <span class="text-2xl block mb-1">💳</span>
                                    <span class="text-sm font-semibold text-light-text">KBZ Pay</span>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="form.payment_method" 
                                       value="manual" 
                                       class="peer hidden">
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all text-center py-4">
                                    <span class="text-2xl block mb-1">🏦</span>
                                    <span class="text-sm font-semibold text-light-text">Manual</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-dark-base rounded-xl p-4 border border-dark-border">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400">Package</span>
                            <span class="text-light-text font-semibold">{{ $package->name ?? 'Package' }}</span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400">Amount</span>
                            <span class="text-light-text font-semibold">{{ number_format($package->price ?? 0) }} Ks</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-dark-border">
                            <span class="text-light-text font-bold">Total</span>
                            <span class="text-secondary text-xl font-bold">{{ number_format($package->price ?? 0) }} Ks</span>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div x-show="error" 
                         x-transition
                         class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm">
                        <span x-text="error"></span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="btn-primary w-full py-4 text-base font-semibold"
                            :disabled="loading || !form.payment_method">
                        <span x-show="!loading">Confirm Order</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function orderFormData() {
    return {
        form: {
            user_id: '',
            server_id: '',
            payment_method: ''
        },
        loading: false,
        error: '',
        
        async submitOrder() {
            this.loading = true;
            this.error = '';
            
            try {
                // Validate required fields
                if (!this.form.user_id || !this.form.user_id.trim()) {
                    this.error = 'Game ID is required';
                    this.loading = false;
                    return;
                }
                
                @php
                    $isMLBB = stripos($game->name ?? '', 'Mobile Legends') !== false || stripos($game->name ?? '', 'MLBB') !== false;
                @endphp
                @if($isMLBB)
                if (!this.form.server_id || !this.form.server_id.trim()) {
                    this.error = 'Zone ID is required';
                    this.loading = false;
                    return;
                }
                @endif
                
                if (!this.form.payment_method) {
                    this.error = 'Please select a payment method';
                    this.loading = false;
                    return;
                }
                
                const res = await fetch('/orders/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        game_id: {{ $game->id ?? 0 }},
                        package_id: {{ $package->id ?? 0 }},
                        user_id: this.form.user_id.trim(),
                        server_id: this.form.server_id ? this.form.server_id.trim() : '',
                        payment_method: this.form.payment_method
                    })
                });
                
                // Check if response is ok
                if (!res.ok) {
                    // Try to parse error response
                    let errorData;
                    try {
                        errorData = await res.json();
                    } catch (e) {
                        throw new Error(`Server error: ${res.status} ${res.statusText}`);
                    }
                    
                    // Handle validation errors
                    if (errorData.errors) {
                        const errorMessages = Object.values(errorData.errors).flat();
                        this.error = errorMessages.join(', ') || 'Validation failed';
                    } else {
                        this.error = errorData.message || `Error: ${res.status} ${res.statusText}`;
                    }
                    this.loading = false;
                    return;
                }
                
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = '/orders/' + data.order_id;
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat();
                        this.error = errorMessages.join(', ') || 'Validation failed';
                    } else {
                        this.error = data.message || 'Failed to create order';
                    }
                }
            } catch (e) {
                console.error('Order creation error:', e);
                this.error = e.message || 'An error occurred. Please check your connection and try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection

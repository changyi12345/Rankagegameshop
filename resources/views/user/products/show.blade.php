@extends('layouts.user')

@section('title', ($product['title'] ?? 'Product') . ' - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-2xl">
    <!-- Back Button -->
    <a href="{{ route('products.index') }}" class="inline-flex items-center text-gray-400 hover:text-primary mb-4 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Products
    </a>

    <!-- Product Details -->
    <div class="card mb-6">
        <div class="mb-4">
            <div class="flex items-center space-x-2 mb-2">
                <h1 class="text-2xl font-bold text-light-text">{{ $product['title'] ?? 'Product' }}</h1>
                @if($isMLBB)
                <span class="badge badge-warning">MLBB Diamonds</span>
                @elseif($isPUBG)
                <span class="badge badge-info">PUBG UC</span>
                @endif
            </div>
            @if(!empty($product['description']))
            <p class="text-gray-400 mb-2">{{ $product['description'] }}</p>
            @endif
            @if(!empty($product['category_title']))
            <span class="badge badge-info">{{ $product['category_title'] }}</span>
            @endif
        </div>

        @php
            $priceUsd = $product['unit_price'] ?? 0;
            $priceKs = $priceUsd * ($exchangeRate ?? 2100);
        @endphp

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-dark-base rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Unit Price</p>
                <p class="text-2xl font-bold text-secondary">
                    {{ number_format($priceKs) }} Ks
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    ${{ number_format($priceUsd, 2) }}
                </p>
            </div>
            <div class="bg-dark-base rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Stock Available</p>
                <p class="text-2xl font-bold {{ ($product['stock'] ?? 0) > 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ number_format($product['stock'] ?? 0) }}
                </p>
            </div>
        </div>

        <!-- Purchase Form -->
        @auth
        <div x-data="productPurchaseData()">
            <form @submit.prevent="purchaseProduct">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        Quantity <span class="text-red-400">*</span>
                    </label>
                    <input type="number" 
                           x-model="quantity" 
                           min="1" 
                           max="100"
                           :max="{{ $product['stock'] ?? 100 }}"
                           required 
                           class="input-field" 
                           placeholder="Enter quantity">
                    <p class="text-xs text-gray-500 mt-1">
                        Available stock: {{ number_format($product['stock'] ?? 0) }}
                    </p>
                </div>

                <div class="bg-dark-base rounded-xl p-4 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-400">Unit Price</span>
                        <span class="text-light-text font-semibold">{{ number_format($priceKs) }} Ks</span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-400">Quantity</span>
                        <span class="text-light-text font-semibold" x-text="quantity || 1"></span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-dark-border">
                        <span class="text-light-text font-bold">Total</span>
                        <span class="text-secondary text-xl font-bold" x-text="formatNumber({{ $priceKs }} * (quantity || 1)) + ' Ks'"></span>
                    </div>
                </div>

                <!-- Error Message -->
                <div x-show="error" 
                     x-transition
                     class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm mb-4">
                    <span x-text="error"></span>
                </div>

                <!-- Success Message -->
                <div x-show="success" 
                     x-transition
                     class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-green-400 text-sm mb-4">
                    <p x-text="successMessage"></p>
                    <div x-show="deliveryItems && deliveryItems.length > 0" class="mt-3">
                        <p class="text-xs text-gray-400 mb-2">Delivery Items:</p>
                        <div class="space-y-1">
                            <template x-for="item in deliveryItems" :key="item">
                                <div class="bg-dark-card rounded p-2 text-xs font-mono text-light-text" x-text="item"></div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="btn-primary w-full py-4 text-base font-semibold"
                        :disabled="loading || ({{ $product['stock'] ?? 0 }} <= 0)">
                    <span x-show="!loading">Purchase Product</span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </form>
        </div>
        @else
        <div class="bg-dark-base rounded-xl p-4 text-center">
            <p class="text-gray-400 mb-4">Please login to purchase products</p>
            <a href="/login" class="btn-primary inline-block">Login</a>
        </div>
        @endauth
    </div>
</div>

@push('scripts')
<script>
function productPurchaseData() {
    return {
        quantity: 1,
        loading: false,
        error: '',
        success: false,
        successMessage: '',
        deliveryItems: [],
        
        formatNumber(num) {
            return new Intl.NumberFormat('en-US').format(Math.round(num));
        },
        
        async purchaseProduct() {
            this.loading = true;
            this.error = '';
            this.success = false;
            this.successMessage = '';
            this.deliveryItems = [];
            
            try {
                const res = await fetch('/products/{{ $product['id'] }}/purchase', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        quantity: this.quantity
                    })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    this.success = true;
                    this.successMessage = data.message || 'Product purchased successfully!';
                    this.deliveryItems = data.delivery_items || [];
                    
                    // Reset form after 3 seconds
                    setTimeout(() => {
                        this.quantity = 1;
                        this.success = false;
                    }, 5000);
                } else {
                    this.error = data.message || 'Purchase failed. Please try again.';
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

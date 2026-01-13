@extends('layouts.user')

@section('title', 'Payment - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-2xl">
    <!-- Back Button -->
    <a href="/orders/{{ $order->id ?? '' }}" class="inline-flex items-center text-gray-400 hover:text-primary mb-4 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Order
    </a>

    <div x-data="paymentData()">
        <!-- Order Summary -->
        <div class="card mb-6">
            <h2 class="text-xl font-bold text-light-text mb-4">Order Summary</h2>
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                        <span class="text-2xl">{{ $order->game->icon ?? '🎮' }}</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-light-text">{{ $order->game->name ?? 'Game' }}</h3>
                        <p class="text-sm text-gray-400">{{ $order->package->name ?? 'Package' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-secondary">{{ number_format($order->amount) }} Ks</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Selection -->
        <div class="card mb-6">
            <h2 class="text-xl font-bold text-light-text mb-4">Select Payment Method</h2>
            
            <div class="space-y-3">
                @auth
                @if(auth()->user()->balance >= $order->amount)
                <label class="cursor-pointer block">
                    <input type="radio" 
                           x-model="paymentMethod" 
                           value="wallet" 
                           class="peer hidden">
                    <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="text-3xl">💰</span>
                                <div>
                                    <h3 class="font-bold text-light-text">Wallet Balance</h3>
                                    <p class="text-sm text-gray-400">Available: {{ number_format(auth()->user()->balance) }} Ks</p>
                                </div>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-gray-600 peer-checked:bg-primary peer-checked:border-primary flex items-center justify-center transition-all">
                                <svg x-show="paymentMethod === 'wallet'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>
                @endif
                @endauth

                <!-- WavePay -->
                <label class="cursor-pointer block">
                    <input type="radio" 
                           x-model="paymentMethod" 
                           value="wavepay" 
                           class="peer hidden">
                    <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="text-3xl">📱</span>
                                <div>
                                    <h3 class="font-bold text-light-text">WavePay</h3>
                                    <p class="text-sm text-gray-400">Pay with WavePay mobile app</p>
                                </div>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-gray-600 peer-checked:bg-primary peer-checked:border-primary flex items-center justify-center transition-all">
                                <svg x-show="paymentMethod === 'wavepay'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- KBZ Pay -->
                <label class="cursor-pointer block">
                    <input type="radio" 
                           x-model="paymentMethod" 
                           value="kpay" 
                           class="peer hidden">
                    <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="text-3xl">💳</span>
                                <div>
                                    <h3 class="font-bold text-light-text">KBZ Pay</h3>
                                    <p class="text-sm text-gray-400">Pay with KBZ Pay app</p>
                                </div>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-gray-600 peer-checked:bg-primary peer-checked:border-primary flex items-center justify-center transition-all">
                                <svg x-show="paymentMethod === 'kpay'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Manual Transfer -->
                <label class="cursor-pointer block">
                    <input type="radio" 
                           x-model="paymentMethod" 
                           value="manual" 
                           class="peer hidden">
                    <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="text-3xl">🏦</span>
                                <div>
                                    <h3 class="font-bold text-light-text">Manual Transfer</h3>
                                    <p class="text-sm text-gray-400">Bank transfer or screenshot</p>
                                </div>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-gray-600 peer-checked:bg-primary peer-checked:border-primary flex items-center justify-center transition-all">
                                <svg x-show="paymentMethod === 'manual'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Payment Details (for manual transfer) -->
        <div x-show="paymentMethod === 'manual'" x-transition class="card mb-6">
            <h3 class="font-bold text-light-text mb-4">Bank Account Details</h3>
            <div class="space-y-3">
                <div class="bg-dark-base rounded-xl p-4">
                    <p class="text-sm text-gray-400 mb-1">Account Name</p>
                    <p class="font-bold text-light-text">RanKage Game Shop</p>
                </div>
                <div class="bg-dark-base rounded-xl p-4">
                    <p class="text-sm text-gray-400 mb-1">Account Number</p>
                    <p class="font-bold text-light-text">1234 5678 9012 3456</p>
                </div>
                <div class="bg-dark-base rounded-xl p-4">
                    <p class="text-sm text-gray-400 mb-1">Bank</p>
                    <p class="font-bold text-light-text">KBZ Bank</p>
                </div>
                <div class="bg-dark-base rounded-xl p-4">
                    <p class="text-sm text-gray-400 mb-1">Amount to Transfer</p>
                    <p class="font-bold text-secondary text-xl">{{ number_format($order->amount) }} Ks</p>
                </div>
            </div>

            <!-- Upload Screenshot -->
            <div class="mt-4">
                <label class="block text-sm font-semibold text-gray-300 mb-2">Upload Payment Screenshot</label>
                <div class="border-2 border-dashed border-dark-border rounded-xl p-6 text-center hover:border-primary transition-colors cursor-pointer">
                    <input type="file" 
                           @change="handleFileUpload($event)" 
                           accept="image/*" 
                           class="hidden" 
                           id="screenshot">
                    <label for="screenshot" class="cursor-pointer">
                        <svg class="w-12 h-12 mx-auto text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-gray-400 text-sm">Click to upload screenshot</p>
                        <p class="text-gray-500 text-xs mt-1">PNG, JPG up to 5MB</p>
                    </label>
                    <div x-show="screenshot" class="mt-4">
                        <img :src="screenshotPreview" class="max-w-full h-32 object-cover rounded-xl mx-auto">
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div x-show="error" 
             x-transition
             class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm mb-6">
            <span x-text="error"></span>
        </div>

        <!-- Pay Button -->
        <button @click="processPayment()" 
                :disabled="loading || !paymentMethod"
                class="btn-primary w-full py-4 text-base font-semibold mb-6">
            <span x-show="!loading">
                <span x-text="paymentMethod === 'wallet' ? 'Pay from Wallet' : paymentMethod === 'manual' ? 'Submit Payment' : 'Continue to Payment'"></span>
            </span>
            <span x-show="loading" class="flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        </button>
    </div>
</div>

@push('scripts')
<script>
function paymentData() {
    return {
        paymentMethod: '',
        screenshot: null,
        screenshotPreview: null,
        loading: false,
        error: '',
        
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    this.error = 'File size must be less than 5MB';
                    return;
                }
                this.screenshot = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.screenshotPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        async processPayment() {
            this.loading = true;
            this.error = '';
            
            try {
                const formData = new FormData();
                formData.append('order_id', {{ $order->id ?? 0 }});
                formData.append('payment_method', this.paymentMethod);
                if (this.screenshot) {
                    formData.append('screenshot', this.screenshot);
                }
                
                const res = await fetch('/payment/process', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await res.json();
                
                if (data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.href = '/orders/' + {{ $order->id ?? 0 }};
                    }
                } else {
                    this.error = data.message || 'Payment failed';
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

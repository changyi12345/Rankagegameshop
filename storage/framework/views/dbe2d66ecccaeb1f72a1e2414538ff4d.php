

<?php $__env->startSection('title', 'Wallet - RanKage Game Shop'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-4 max-w-2xl">
    <h1 class="text-2xl font-bold text-light-text mb-6 flex items-center">
        <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
        My Wallet
    </h1>

    <div x-data="walletData()">
        <!-- Balance Card -->
        <div class="card mb-6 bg-gradient-to-br from-dark-card via-dark-base to-dark-card">
            <div class="text-center">
                <p class="text-gray-400 text-sm mb-2">Current Balance</p>
                <p class="text-5xl font-bold text-secondary mb-4"><?php echo e(number_format(auth()->user()->balance ?? 0)); ?> <span class="text-2xl text-gray-400">Ks</span></p>
                <button @click="showTopUp = true" class="btn-primary px-8 py-3">
                    Top Up Wallet
                </button>
            </div>
        </div>

        <!-- Top Up Form -->
        <div x-show="showTopUp" x-transition class="card mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-light-text">Top Up Wallet</h2>
                <button @click="showTopUp = false" class="text-gray-400 hover:text-light-text">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="processTopUp" enctype="multipart/form-data">
                <div class="space-y-4">
                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Amount (Ks)</label>
                        <input type="number" 
                               x-model="topUpForm.amount" 
                               required 
                               min="1000"
                               step="1000"
                               class="input-field" 
                               placeholder="Enter amount (minimum 1,000 Ks)">
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" @click="topUpForm.amount = 5000" class="btn-outline py-2 text-sm">5,000</button>
                        <button type="button" @click="topUpForm.amount = 10000" class="btn-outline py-2 text-sm">10,000</button>
                        <button type="button" @click="topUpForm.amount = 20000" class="btn-outline py-2 text-sm">20,000</button>
                        <button type="button" @click="topUpForm.amount = 50000" class="btn-outline py-2 text-sm">50,000</button>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Payment Method</label>
                        <div class="grid grid-cols-1 gap-3">
                            <!-- Manual Bank Transfer (Available) -->
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="topUpForm.payment_method" 
                                       value="manual" 
                                       class="peer hidden"
                                       checked>
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all text-center py-3 relative">
                                    <span class="text-2xl block mb-1">🏦</span>
                                    <span class="text-sm font-semibold text-light-text">Manual Bank Transfer</span>
                                    <span class="absolute top-2 right-2 text-xs bg-secondary/20 text-secondary px-2 py-0.5 rounded-full">Available</span>
                                </div>
                            </label>
                            
                            <!-- WavePay (Coming Soon) -->
                            <label class="cursor-pointer opacity-60">
                                <input type="radio" 
                                       x-model="topUpForm.payment_method" 
                                       value="wavepay" 
                                       class="peer hidden"
                                       disabled>
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 text-center py-3 relative cursor-not-allowed">
                                    <span class="text-2xl block mb-1">📱</span>
                                    <span class="text-sm font-semibold text-gray-500">WavePay</span>
                                    <span class="absolute top-2 right-2 text-xs bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded-full">Coming Soon</span>
                                </div>
                            </label>
                            
                            <!-- KBZ Pay (KPay) -->
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="topUpForm.payment_method"
                                       value="kpay" 
                                       class="peer hidden">
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all text-center py-3 relative">
                                    <span class="text-2xl block mb-1">💳</span>
                                    <span class="text-sm font-semibold text-light-text">KBZ Pay (KPay)</span>
                                    <span class="absolute top-2 right-2 text-xs bg-secondary/20 text-secondary px-2 py-0.5 rounded-full">Available</span>
                                </div>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">💡 Select your preferred payment method</p>
                    </div>

                    <!-- KPay Payment Instructions -->
                    <div id="kpay-payment" x-show="topUpForm.payment_method === 'kpay'" x-transition class="bg-dark-base rounded-xl p-4 border border-dark-border space-y-4">
                        <h3 class="font-bold text-light-text mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            KPay Payment Details
                        </h3>
                        
                        <?php if(isset($kpayBank) && $kpayBank): ?>
                            <div class="bg-dark-card rounded-xl p-4 border border-dark-border">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-light-text"><?php echo e($kpayBank->bank_name); ?></h4>
                                    <?php if($kpayBank->qr_code): ?>
                                        <span class="badge badge-info text-xs">QR Available</span>
                                    <?php endif; ?>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between items-center py-2 border-b border-dark-border">
                                        <span class="text-gray-400">Account Name:</span>
                                        <span class="text-light-text font-semibold"><?php echo e($kpayBank->account_name); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-dark-border">
                                        <span class="text-gray-400">Account Number:</span>
                                        <span class="text-light-text font-semibold font-mono"><?php echo e($kpayBank->account_number); ?></span>
                                    </div>
                                    <?php if($kpayBank->qr_code): ?>
                                    <div class="flex justify-center pt-2">
                                        <img src="<?php echo e(asset('storage/' . $kpayBank->qr_code)); ?>" alt="QR Code" class="w-40 h-40 object-contain bg-white rounded-lg p-2">
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-dark-card rounded-xl p-4 border border-dark-border">
                                <p class="text-gray-400 text-sm">KPay account details will be displayed here. Please contact admin if not visible.</p>
                            </div>
                        <?php endif; ?>

                        <!-- KPay Account Number Input -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Your KPay Account Number <span class="text-red-400">*</span>
                            </label>
                            <input type="text" 
                                   x-model="topUpForm.kpay_account"
                                   :required="topUpForm.payment_method === 'kpay'"
                                   class="input-field"
                                   placeholder="Enter your KPay account number">
                            <p class="text-xs text-gray-500 mt-1">Enter the KPay account number you used for payment</p>
                        </div>

                        <div class="flex justify-between items-center py-2 pt-3 bg-primary/5 rounded-lg px-3">
                            <span class="text-gray-300 font-semibold">Amount to Pay:</span>
                            <span class="text-secondary text-xl font-bold" x-text="topUpForm.amount ? numberFormat(topUpForm.amount) + ' Ks' : '0 Ks'"></span>
                        </div>
                        
                        <!-- Screenshot Upload -->
                        <div class="mt-4 pt-4 border-t border-dark-border">
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Payment Screenshot <span class="text-red-400">*</span>
                            </label>
                            <div class="space-y-3">
                                <div class="relative">
                                    <input type="file" 
                                           name="screenshot" 
                                           id="kpay-screenshot"
                                           accept="image/*"
                                           @change="handleScreenshotUpload"
                                           class="hidden">
                                    <label for="kpay-screenshot" 
                                           class="flex items-center justify-center space-x-2 cursor-pointer border-2 border-dashed border-dark-border rounded-xl p-6 hover:border-primary transition-colors">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div class="text-center">
                                            <p class="text-sm font-semibold text-light-text">Click to upload payment screenshot</p>
                                            <p class="text-xs text-gray-500">PNG, JPG up to 5MB</p>
                                        </div>
                                    </label>
                                </div>
                                <div x-show="topUpForm.screenshotPreview" class="relative">
                                    <img :src="topUpForm.screenshotPreview" alt="Screenshot Preview" class="w-full max-w-md mx-auto rounded-lg">
                                    <button type="button" @click="removeScreenshot" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Payment Instructions -->
                    <div id="manual-payment" x-show="topUpForm.payment_method === 'manual'" x-transition class="bg-dark-base rounded-xl p-4 border border-dark-border space-y-4">
                        <h3 class="font-bold text-light-text mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Bank Account Details
                        </h3>
                        
                        <?php if(isset($activeBanks) && $activeBanks->count() > 0): ?>
                            <div class="space-y-4">
                                <?php $__currentLoopData = $activeBanks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-dark-card rounded-xl p-4 border border-dark-border">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-bold text-light-text"><?php echo e($bank->bank_name); ?></h4>
                                        <?php if($bank->qr_code): ?>
                                            <span class="badge badge-info text-xs">QR Available</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between items-center py-2 border-b border-dark-border">
                                            <span class="text-gray-400">Account Name:</span>
                                            <span class="text-light-text font-semibold"><?php echo e($bank->account_name); ?></span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-dark-border">
                                            <span class="text-gray-400">Account Number:</span>
                                            <span class="text-light-text font-semibold font-mono"><?php echo e($bank->account_number); ?></span>
                                        </div>
                                        <?php if($bank->qr_code): ?>
                                        <div class="flex justify-center pt-2">
                                            <img src="<?php echo e(asset('storage/' . $bank->qr_code)); ?>" alt="QR Code" class="w-40 h-40 object-contain bg-white rounded-lg p-2">
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                
                                <div class="flex justify-between items-center py-2 pt-3 bg-primary/5 rounded-lg px-3 mt-4">
                                    <span class="text-gray-300 font-semibold">Amount to Transfer:</span>
                                    <span class="text-secondary text-xl font-bold" x-text="topUpForm.amount ? numberFormat(topUpForm.amount) + ' Ks' : '0 Ks'"></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-gray-400">
                                <p>No bank accounts available. Please contact support.</p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Screenshot Upload -->
                        <div class="mt-4 pt-4 border-t border-dark-border">
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Payment Screenshot <span class="text-red-400">*</span>
                            </label>
                            <div class="space-y-3">
                                <div class="relative">
                                    <input type="file" 
                                           name="screenshot" 
                                           id="screenshot"
                                           accept="image/*"
                                           @change="handleScreenshotUpload"
                                           class="hidden">
                                    <label for="screenshot" 
                                           class="flex items-center justify-center space-x-2 cursor-pointer border-2 border-dashed border-dark-border rounded-xl p-6 hover:border-primary transition-colors">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div class="text-center">
                                            <p class="text-sm font-semibold text-light-text">Click to upload screenshot</p>
                                            <p class="text-xs text-gray-500">PNG, JPG up to 5MB</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <!-- Preview -->
                                <div x-show="topUpForm.screenshotPreview" class="mt-3">
                                    <img :src="topUpForm.screenshotPreview" 
                                         alt="Screenshot preview" 
                                         class="w-full h-48 object-cover rounded-xl border border-dark-border">
                                    <button type="button" 
                                            @click="removeScreenshot"
                                            class="mt-2 text-sm text-red-400 hover:text-red-300">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                            <p class="text-xs text-yellow-400 flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>After transferring, upload your payment screenshot above. Your wallet will be credited within 24 hours after admin approval.</span>
                            </p>
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
                            class="btn-primary w-full py-3"
                            :disabled="loading || !topUpForm.payment_method || 
                                (topUpForm.payment_method === 'manual' && !topUpForm.screenshot) ||
                                (topUpForm.payment_method === 'kpay' && (!topUpForm.kpay_account || !topUpForm.screenshot))">
                        <span x-show="!loading">Continue to Payment</span>
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

        <!-- View Transaction History Link -->
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-light-text mb-1">Transaction History</h3>
                    <p class="text-sm text-gray-400">View all your transaction history</p>
                </div>
                <a href="<?php echo e(route('transactions.index')); ?>" class="btn-primary px-6 py-3">
                    View All Transactions
                </a>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function walletData() {
    return {
        showTopUp: false,
        topUpForm: {
            amount: '',
            payment_method: 'manual', // Default to manual
            kpay_account: '',
            screenshot: null,
            screenshotPreview: null
        },
        loading: false,
        error: '',
        
        numberFormat(num) {
            if (!num) return '0';
            return new Intl.NumberFormat('en-US').format(num);
        },
        
        handleScreenshotUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    this.error = 'File size must be less than 5MB';
                    return;
                }
                
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    this.error = 'Please upload an image file';
                    return;
                }
                
                this.topUpForm.screenshot = file;
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.topUpForm.screenshotPreview = e.target.result;
                };
                reader.readAsDataURL(file);
                this.error = '';
            }
        },
        
        removeScreenshot() {
            this.topUpForm.screenshot = null;
            this.topUpForm.screenshotPreview = null;
            document.getElementById('screenshot').value = '';
        },
        
        async processTopUp() {
            this.loading = true;
            this.error = '';
            
            if (!this.topUpForm.amount || this.topUpForm.amount < 1000) {
                this.error = 'Minimum top-up amount is 1,000 Ks';
                this.loading = false;
                return;
            }
            
            if (!this.topUpForm.payment_method) {
                this.error = 'Please select a payment method';
                this.loading = false;
                return;
            }
            
            // Validate screenshot for manual payment
            if (this.topUpForm.payment_method === 'manual' && !this.topUpForm.screenshot) {
                this.error = 'Please upload payment screenshot';
                this.loading = false;
                return;
            }

            // Validate KPay payment
            if (this.topUpForm.payment_method === 'kpay') {
                if (!this.topUpForm.kpay_account) {
                    this.error = 'Please enter your KPay account number.';
                    this.loading = false;
                    return;
                }
                if (!this.topUpForm.screenshot) {
                    this.error = 'Please upload a payment screenshot for KPay.';
                    this.loading = false;
                    return;
                }
            }
            
            try {
                    const formData = new FormData();
                    formData.append('amount', this.topUpForm.amount);
                    formData.append('payment_method', this.topUpForm.payment_method);
                    if (this.topUpForm.kpay_account) {
                        formData.append('kpay_account', this.topUpForm.kpay_account);
                    }
                    if (this.topUpForm.screenshot) {
                        formData.append('screenshot', this.topUpForm.screenshot);
                    }
                
                const res = await fetch('/wallet/top-up', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await res.json();
                
                if (data.success) {
                    // Show success message
                    this.error = '';
                    alert(data.message || 'Top-up request submitted successfully! Your wallet will be credited after admin approval.');
                    // Reset form
                    this.topUpForm.amount = '';
                    this.removeScreenshot();
                    this.showTopUp = false;
                    // Reload page to show updated balance
                    window.location.reload();
                } else {
                    this.error = data.message || 'Failed to process top-up';
                }
            } catch (e) {
                console.error('Top-up error:', e);
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/user/wallet.blade.php ENDPATH**/ ?>
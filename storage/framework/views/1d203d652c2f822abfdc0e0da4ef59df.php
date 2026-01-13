

<?php $__env->startSection('title', 'API Settings - Admin Panel'); ?>
<?php $__env->startSection('page-title', 'G2Bulk API Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div x-data="apiSettingsData()">
        <!-- API Configuration -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">API Configuration</h2>
            
            <form @submit.prevent="saveSettings">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">API Key <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input :type="showApiKey ? 'text' : 'password'" 
                                   x-model="form.api_key" 
                                   class="input-field pr-12" 
                                   placeholder="Enter your G2Bulk API Key"
                                   required>
                            <button type="button" 
                                    @click="showApiKey = !showApiKey"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-light-text">
                                <svg x-show="!showApiKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showApiKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.487 5.197m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Get your API key from G2Bulk Telegram Bot</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">API Base URL <span class="text-red-400">*</span></label>
                        <input type="url" 
                               x-model="form.api_url" 
                               class="input-field" 
                               placeholder="https://api.g2bulk.com/v1"
                               required>
                        <p class="text-xs text-gray-500 mt-1">G2Bulk API endpoint URL</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Callback URL (Optional)</label>
                        <input type="url" 
                               x-model="form.callback_url" 
                               class="input-field" 
                               placeholder="https://your-domain.com/webhook/g2bulk">
                        <p class="text-xs text-gray-500 mt-1">Webhook URL for order status updates (e.g., ngrok URL)</p>
                    </div>

                    <div class="pt-4 border-t border-dark-border">
                        <h3 class="text-lg font-bold text-light-text mb-4">Price Settings</h3>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                USD to Myanmar Kyat (Ks) Exchange Rate <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       x-model="form.usd_to_kyat_rate" 
                                       min="1" 
                                       max="10000"
                                       step="0.01"
                                       class="input-field pr-20" 
                                       placeholder="2100"
                                       required>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Ks per USD</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                This rate is used to convert USD prices to Myanmar Kyat (Ks) for products and games.
                                <br>
                                <span class="text-yellow-400">Example: 1 USD = <span x-text="form.usd_to_kyat_rate || 2100"></span> Ks</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-dark-border">
                        <div>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       x-model="form.auto_retry" 
                                       class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                                <span class="ml-2 text-sm text-gray-300">Enable Auto Retry</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-6">Automatically retry failed orders</p>
                        </div>
                    </div>

                    <div x-show="form.auto_retry">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Max Retry Attempts</label>
                        <input type="number" 
                               x-model="form.max_retries" 
                               min="1" 
                               max="10"
                               class="input-field">
                    </div>

                    <div x-show="error" 
                         x-transition
                         class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm">
                        <span x-text="error"></span>
                    </div>

                    <div x-show="success" 
                         x-transition
                         class="bg-secondary/10 border border-secondary/30 rounded-xl p-4 text-secondary text-sm">
                        <span>Settings saved successfully!</span>
                    </div>

                    <button type="submit" 
                            class="btn-primary w-full py-3"
                            :disabled="loading">
                        <span x-show="!loading">Save Settings</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- API Status -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">API Status</h2>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-dark-base rounded-xl">
                    <div>
                        <p class="text-light-text font-semibold">Connection Status</p>
                        <p class="text-gray-400 text-sm">Last checked: <?php echo e(now()->format('M d, Y h:i A')); ?></p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="badge badge-success">Connected</span>
                        <button @click="testConnection()" 
                                class="btn-outline px-4 py-2 text-sm"
                                :disabled="checking">
                            <span x-show="!checking">Test Connection</span>
                            <span x-show="checking">Testing...</span>
                        </button>
                    </div>
                </div>

                <div class="bg-dark-base rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-400">G2Bulk Balance</span>
                        <span class="text-2xl font-bold text-secondary" x-text="balance || '0'">0</span>
                    </div>
                    <button @click="checkBalance()" 
                            class="btn-outline w-full mt-3 py-2 text-sm"
                            :disabled="checkingBalance">
                        <span x-show="!checkingBalance">Check Balance</span>
                        <span x-show="checkingBalance" class="flex items-center justify-center">
                            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Checking...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Logs -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-light-text">Recent Error Logs</h2>
                <button class="btn-outline px-4 py-2 text-sm">Clear Logs</button>
            </div>
            
            <div class="space-y-2 max-h-96 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $error_logs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-dark-base rounded-xl p-3 border-l-4 border-red-500">
                    <div class="flex items-start justify-between mb-1">
                        <p class="text-red-400 text-sm font-semibold"><?php echo e($log->error_type ?? 'Error'); ?></p>
                        <p class="text-gray-500 text-xs"><?php echo e($log->created_at->format('M d, h:i A')); ?></p>
                    </div>
                    <p class="text-gray-400 text-xs"><?php echo e($log->error_message ?? $log->message ?? 'No error message available'); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8 text-gray-400">
                    <span class="text-4xl block mb-2">✅</span>
                    <p>No error logs</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function apiSettingsData() {
    return {
        showApiKey: false,
        form: {
            api_key: <?php echo json_encode($settings->api_key ?? '', 15, 512) ?>,
            api_url: <?php echo json_encode($settings->api_url ?? 'https://api.g2bulk.com/v1', 15, 512) ?>,
            callback_url: <?php echo json_encode($settings->callback_url ?? '', 15, 512) ?>,
            auto_retry: <?php echo e($settings->auto_retry ?? false ? 'true' : 'false'); ?>,
            max_retries: <?php echo e($settings->max_retries ?? 3); ?>,
            usd_to_kyat_rate: <?php echo e($settings->usd_to_kyat_rate ?? 2100); ?>

        },
        balance: null,
        checking: false,
        checkingBalance: false,
        loading: false,
        error: '',
        success: false,
        
        async saveSettings() {
            this.loading = true;
            this.error = '';
            this.success = false;
            
            try {
                const res = await fetch('/admin/api-settings/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    this.success = true;
                    setTimeout(() => this.success = false, 3000);
                } else {
                    this.error = data.message || 'Failed to save settings';
                }
            } catch (e) {
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        },
        
        async testConnection() {
            this.checking = true;
            try {
                const res = await fetch('/admin/api-settings/test-connection', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert('✅ API Connection Successful!\n\nBalance: ' + data.balance + ' USD');
                } else {
                    alert('❌ Connection Failed:\n' + data.message);
                }
            } catch (e) {
                alert('❌ Error: ' + e.message);
            } finally {
                this.checking = false;
            }
        },
        
        async checkBalance() {
            this.checkingBalance = true;
            try {
                const res = await fetch('/admin/api-settings/check-balance', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.balance = data.balance;
                }
            } catch (e) {
                alert('Failed to check balance');
            } finally {
                this.checkingBalance = false;
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/admin/api-settings/index.blade.php ENDPATH**/ ?>
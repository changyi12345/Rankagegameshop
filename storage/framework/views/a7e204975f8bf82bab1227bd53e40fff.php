

<?php $__env->startSection('title', ($game->name ?? 'Game') . ' - RanKage Game Shop'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-4">
    <!-- Back Button -->
    <a href="/" class="inline-flex items-center text-gray-400 hover:text-primary mb-4 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back
    </a>

    <!-- Game Header -->
    <div class="card mb-6 bg-gradient-to-br from-dark-card to-dark-base overflow-hidden">
        <div class="flex items-center space-x-4">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center flex-shrink-0">
                <span class="text-5xl"><?php echo e($game->icon ?? '🎮'); ?></span>
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-light-text mb-1"><?php echo e($game->name ?? 'Game'); ?></h1>
                <p class="text-gray-400 text-sm"><?php echo e($game->currency_name ?? 'Currency'); ?></p>
                <?php if($game->is_active ?? true): ?>
                    <span class="badge badge-success mt-2">Available</span>
                <?php else: ?>
                    <span class="badge badge-danger mt-2">Unavailable</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Packages Grid -->
    <div x-data="gameDetailData()">
        <h2 class="text-xl font-bold text-light-text mb-4 flex items-center">
            <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
            Select Package
        </h2>

        <div class="grid grid-cols-1 gap-4 mb-6">
            <?php
                $exchangeRate = \App\Models\Setting::get('usd_to_kyat_rate', 2100);
                $allPackages = collect($packages ?? []);
                
                // Add G2Bulk packages if available
                if (!empty($g2bulkPackages)) {
                    foreach ($g2bulkPackages as $g2pkg) {
                        $allPackages->push((object) $g2pkg);
                    }
                }
            ?>
            
            <?php $__empty_1 = true; $__currentLoopData = $allPackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $packageId = $package->id ?? ($package['id'] ?? null);
                // For G2Bulk packages, format as 'g2bulk_{id}'
                if (isset($package['is_g2bulk']) && $package['is_g2bulk']) {
                    $packageId = 'g2bulk_' . ($package['id'] ?? $packageId);
                }
                $packageName = $package->name ?? ($package['name'] ?? 'Package');
                $packagePrice = $package->price ?? ($package['price'] ?? 0);
                $currencyAmount = $package->currency_amount ?? ($package['currency_amount'] ?? '');
                $bonus = $package->bonus ?? ($package['bonus'] ?? 0);
                $isG2Bulk = $package->is_g2bulk ?? ($package['is_g2bulk'] ?? false);
            ?>
            <?php if($packageId): ?>
            <div @click="selectPackage('<?php echo e($packageId); ?>')"
                 :class="selectedPackage === '<?php echo e($packageId); ?>' ? 'ring-2 ring-primary bg-primary/5' : ''"
                 class="card cursor-pointer hover:bg-dark-base transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-bold text-light-text"><?php echo e($packageName); ?></h3>
                            <?php if($bonus > 0): ?>
                                <span class="badge badge-success">+<?php echo e($bonus); ?> Bonus</span>
                            <?php endif; ?>
                            <?php if($isG2Bulk): ?>
                                <span class="badge badge-info text-xs">G2Bulk</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div>
                                <p class="text-2xl font-bold text-primary"><?php echo e($currencyAmount); ?></p>
                                <p class="text-xs text-gray-400"><?php echo e($game->currency_name ?? 'Currency'); ?></p>
                            </div>
                            <div class="text-gray-500">→</div>
                            <div>
                                <p class="text-xl font-bold text-secondary"><?php echo e(number_format($packagePrice)); ?></p>
                                <p class="text-xs text-gray-400">Ks</p>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div :class="selectedPackage === '<?php echo e($packageId); ?>' ? 'bg-primary border-primary' : 'bg-dark-base border-dark-border'"
                             class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all">
                            <svg x-show="selectedPackage === '<?php echo e($packageId); ?>'" 
                                 class="w-4 h-4 text-white" 
                                 fill="currentColor" 
                                 viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="card text-center py-8">
                <span class="text-4xl mb-3 block">📦</span>
                <p class="text-gray-400">No packages available</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Order Form -->
        <div x-show="!!selectedPackage" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="card mb-6">
            <h2 class="text-xl font-bold text-light-text mb-4 flex items-center">
                <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
                Order Details
            </h2>
            
            <form @submit.prevent="submitOrder">
                <div class="space-y-4">
                    <?php
                        $gameName = strtolower(trim($game->name ?? ''));
                        $isMLBB = $gameName === 'mobile legends' 
                                || stripos($gameName, 'mobile legends') !== false 
                                || stripos($gameName, 'mlbb') !== false
                                || stripos($gameName, 'mobile legend') !== false
                                || $game->id == 1;
                    ?>
                    
                    <?php if($isMLBB): ?>
                    <!-- Mobile Legends: Game ID and Zone ID side by side -->
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
                                   @input="form.user_id = form.user_id.replace(/[^0-9]/g, ''); checkPlayerId()"
                                   @blur="checkPlayerId()"
                                   maxlength="20">
                            <p class="text-xs text-gray-500 mt-1">
                                Your Mobile Legends Game ID
                            </p>
                            <!-- Player Name Display -->
                            <div x-show="playerName" x-transition class="mt-2">
                                <div class="inline-block px-3 py-1.5 bg-green-500/10 border border-green-500/30 rounded-lg">
                                    <span class="text-xs text-gray-400">In-Game Name: </span>
                                    <span class="text-sm font-bold text-green-400" x-text="playerName"></span>
                                </div>
                            </div>
                            <div x-show="playerCheckError" x-transition class="mt-2">
                                <div class="inline-flex items-center space-x-2 px-3 py-1.5 bg-red-500/10 border border-red-500/30 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <p class="text-xs text-red-400" x-text="playerCheckError"></p>
                                </div>
                            </div>
                            <div x-show="checkingPlayer" x-transition class="mt-2">
                                <div class="inline-flex items-center space-x-2 px-3 py-1.5 bg-primary/10 border border-primary/30 rounded-lg">
                                    <div class="relative w-3.5 h-3.5">
                                        <div class="absolute inset-0 border-2 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                                    </div>
                                    <span class="text-xs text-gray-400">Verifying...</span>
                                </div>
                            </div>
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
                            <?php if(!empty($availableServers)): ?>
                            <select x-model="form.server_id" 
                                    required 
                                    class="input-field w-full">
                                <option value="">Select Server</option>
                                <?php $__currentLoopData = $availableServers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serverKey => $serverValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($serverKey); ?>"><?php echo e($serverValue); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php else: ?>
                            <input type="text" 
                                   x-model="form.server_id" 
                                   required 
                                   class="input-field w-full uppercase" 
                                   placeholder="ZONE ID"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   @input="form.server_id = form.server_id.replace(/[^0-9]/g, ''); checkPlayerId()"
                                   @blur="checkPlayerId()"
                                   maxlength="10">
                            <?php endif; ?>
                            <p class="text-xs text-gray-500 mt-1">
                                Your Mobile Legends Zone ID
                            </p>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Other Games: User ID and Server ID -->
                    <?php
                        $gameNameLower = strtolower(trim($game->name ?? ''));
                        $isPUBG = stripos($gameNameLower, 'pubg') !== false;
                        $isHOK = stripos($gameNameLower, 'honor of kings') !== false 
                                || stripos($gameNameLower, 'hok') !== false
                                || stripos($gameNameLower, 'arena of valor') !== false;
                        $supportsPlayerCheck = $isPUBG || $isHOK;
                    ?>
                    <!-- User ID -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            User ID <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               x-model="form.user_id" 
                               required 
                               class="input-field" 
                               placeholder="Enter your game User ID"
                               <?php if($supportsPlayerCheck): ?>
                               @input="checkPlayerId()"
                               @blur="checkPlayerId()"
                               <?php endif; ?>>
                        <p class="text-xs text-gray-500 mt-1">Your game account ID</p>
                        <?php if($supportsPlayerCheck): ?>
                        <!-- Player Name Display -->
                        <div x-show="playerName" x-transition class="mt-2">
                            <div class="inline-block px-3 py-1.5 bg-green-500/10 border border-green-500/30 rounded-lg">
                                <span class="text-xs text-gray-400">In-Game Name: </span>
                                <span class="text-sm font-bold text-green-400" x-text="playerName"></span>
                            </div>
                        </div>
                        <div x-show="playerCheckError" x-transition class="mt-2">
                            <div class="inline-flex items-center space-x-2 px-3 py-1.5 bg-red-500/10 border border-red-500/30 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <p class="text-xs text-red-400" x-text="playerCheckError"></p>
                            </div>
                        </div>
                        <div x-show="checkingPlayer" x-transition class="mt-2">
                            <div class="inline-flex items-center space-x-2 px-3 py-1.5 bg-primary/10 border border-primary/30 rounded-lg">
                                <div class="relative w-3.5 h-3.5">
                                    <div class="absolute inset-0 border-2 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                                </div>
                                <span class="text-xs text-gray-400">Verifying...</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Server ID (if required) -->
                    <?php if($game->requires_server ?? false): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Server ID <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               x-model="form.server_id" 
                               required 
                               class="input-field" 
                               placeholder="Enter Server ID">
                        <p class="text-xs text-gray-500 mt-1">Your game server ID</p>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Payment Method <span class="text-red-400">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <?php if(auth()->guard()->check()): ?>
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="form.payment_method" 
                                       value="wallet" 
                                       class="peer hidden">
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all">
                                    <div class="text-center">
                                        <span class="text-2xl block mb-1">💰</span>
                                        <span class="text-sm font-semibold text-light-text">Wallet</span>
                                        <p class="text-xs text-gray-400 mt-1" x-text="walletBalance ? walletBalance + ' Ks' : ''"></p>
                                    </div>
                                </div>
                            </label>
                            <?php endif; ?>
                            
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="form.payment_method" 
                                       value="wavepay" 
                                       class="peer hidden">
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all">
                                    <div class="text-center">
                                        <span class="text-2xl block mb-1">📱</span>
                                        <span class="text-sm font-semibold text-light-text">WavePay</span>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="form.payment_method" 
                                       value="kpay" 
                                       class="peer hidden">
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all">
                                    <div class="text-center">
                                        <span class="text-2xl block mb-1">💳</span>
                                        <span class="text-sm font-semibold text-light-text">KBZ Pay</span>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       x-model="form.payment_method" 
                                       value="manual" 
                                       class="peer hidden">
                                <div class="card peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 hover:bg-dark-base transition-all">
                                    <div class="text-center">
                                        <span class="text-2xl block mb-1">🏦</span>
                                        <span class="text-sm font-semibold text-light-text">Manual</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-dark-base rounded-xl p-4 border border-dark-border">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400">Package</span>
                            <span class="text-light-text font-semibold" x-text="selectedPackageName"></span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400">Amount</span>
                            <span class="text-light-text font-semibold" x-text="selectedPackagePrice ? selectedPackagePrice + ' Ks' : '-'"></span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-dark-border">
                            <span class="text-light-text font-bold">Total</span>
                            <span class="text-secondary text-xl font-bold" x-text="selectedPackagePrice ? selectedPackagePrice + ' Ks' : '-'"></span>
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
                            :disabled="loading || !selectedPackage || !form.payment_method">
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

<?php $__env->startPush('scripts'); ?>
<script>
function gameDetailData() {
    return {
        selectedPackage: null,
        selectedPackageName: '',
        selectedPackagePrice: null,
        walletBalance: <?php echo e(auth()->check() ? (auth()->user()->balance ?? 0) : 0); ?>,
        form: {
            user_id: '',
            server_id: '',
            payment_method: ''
        },
        loading: false,
        error: '',
        packages: <?php echo json_encode($allPackages ?? [], 15, 512) ?>,
        gameCode: '<?php echo e($gameCode ?? ''); ?>',
        requiredFields: <?php echo json_encode($requiredFields ?? [], 15, 512) ?>,
        availableServers: <?php echo json_encode($availableServers ?? [], 15, 512) ?>,
        fieldNotes: '<?php echo e($fieldNotes ?? ''); ?>',
        playerName: '',
        playerCheckError: '',
        checkingPlayer: false,
        checkPlayerTimeout: null,
        
        selectPackage(packageId) {
            // Convert packageId to string for consistent comparison
            packageId = String(packageId);
            console.log('Selecting package:', packageId);
            
            // Set selected package immediately
            this.selectedPackage = packageId;
            
            // Find package in the packages array
            // Check for both regular packages and G2Bulk packages
            let pkg = this.packages.find(p => {
                const pId = String(p.id ?? p['id'] ?? '');
                // Check if it's a G2Bulk package
                if (packageId.startsWith('g2bulk_')) {
                    const g2bulkId = packageId.replace('g2bulk_', '');
                    return pId === g2bulkId || (p.is_g2bulk && pId === g2bulkId);
                }
                return pId === packageId;
            });
            
            // If not found, try to find by checking if packageId matches the format
            if (!pkg && packageId.startsWith('g2bulk_')) {
                const g2bulkId = packageId.replace('g2bulk_', '');
                pkg = this.packages.find(p => {
                    const pId = String(p.id ?? p['id'] ?? '');
                    return pId === g2bulkId;
                });
            }
            
            console.log('Found package:', pkg);
            console.log('All packages:', this.packages);
            console.log('Selected package value:', this.selectedPackage);
            
            if (pkg) {
                this.selectedPackageName = pkg.name ?? pkg['name'] ?? 'Package';
                this.selectedPackagePrice = pkg.price ?? pkg['price'] ?? 0;
                
                console.log('Package selected:', {
                    id: packageId,
                    name: this.selectedPackageName,
                    price: this.selectedPackagePrice
                });
                
                // Force Alpine.js to update
                this.$nextTick(() => {
                    // Scroll to form after a short delay
                    setTimeout(() => {
                        const formElement = document.querySelector('[x-show*="selectedPackage"]');
                        if (formElement) {
                            formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }, 200);
                });
            } else {
                console.error('Package not found:', packageId, this.packages);
                // Still set the package ID so form can show
                this.selectedPackageName = 'Selected Package';
                this.selectedPackagePrice = 0;
            }
        },
        
        async checkPlayerId() {
            // Check for games that support player ID verification
            <?php
                $gameName = strtolower(trim($game->name ?? ''));
                $supportsPlayerCheck = stripos($gameName, 'mobile legends') !== false 
                        || stripos($gameName, 'mlbb') !== false
                        || stripos($gameName, 'mobile legend') !== false
                        || stripos($gameName, 'pubg') !== false
                        || stripos($gameName, 'honor of kings') !== false
                        || stripos($gameName, 'hok') !== false
                        || stripos($gameName, 'arena of valor') !== false
                        || ($game->id ?? 0) == 1;
            ?>
            
            <?php if($supportsPlayerCheck): ?>
            // Clear previous timeout
            if (this.checkPlayerTimeout) {
                clearTimeout(this.checkPlayerTimeout);
            }
            
            // Reset states
            this.playerName = '';
            this.playerCheckError = '';
            this.checkingPlayer = false;
            
            // Check if User ID is filled
            if (!this.form.user_id) {
                return;
            }
            
            // Minimum length check for User ID
            if (this.form.user_id.length < 5) {
                return;
            }
            
            // For MLBB, check if Zone ID is also filled
            <?php
                $gameNameLower = strtolower(trim($game->name ?? ''));
                $isMLBB = stripos($gameNameLower, 'mobile legends') !== false 
                        || stripos($gameNameLower, 'mlbb') !== false
                        || stripos($gameNameLower, 'mobile legend') !== false
                        || ($game->id ?? 0) == 1;
            ?>
            
            <?php if($isMLBB): ?>
            if (!this.form.server_id || this.form.server_id.length < 2) {
                return;
            }
            <?php endif; ?>
            
            // Debounce: wait 800ms after user stops typing
            this.checkPlayerTimeout = setTimeout(async () => {
                this.checkingPlayer = true;
                
                // Set timeout to stop loading after 10 seconds
                const timeoutId = setTimeout(() => {
                    this.checkingPlayer = false;
                    this.playerCheckError = 'Request timeout. Please try again.';
                }, 10000);
                
                try {
                    const controller = new AbortController();
                    const timeout = setTimeout(() => controller.abort(), 8000); // 8 second timeout
                    
                    const res = await fetch('/games/check-player-id', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            game_id: <?php echo e($game->id ?? 0); ?>,
                            user_id: this.form.user_id,
                            server_id: this.form.server_id || null
                        }),
                        signal: controller.signal
                    });
                    
                    clearTimeout(timeout);
                    clearTimeout(timeoutId);
                    
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    
                    const data = await res.json();
                    
                    if (data.success && data.valid) {
                        this.playerName = data.player_name || 'Player';
                        this.playerCheckError = '';
                    } else {
                        this.playerName = '';
                        this.playerCheckError = data.message || 'Invalid Game ID or Zone ID';
                    }
                } catch (e) {
                    clearTimeout(timeoutId);
                    if (e.name === 'AbortError') {
                        this.playerCheckError = 'Request timeout. Please check your connection and try again.';
                    } else {
                        this.playerCheckError = 'Failed to verify player ID. Please try again.';
                    }
                    this.playerName = '';
                } finally {
                    this.checkingPlayer = false;
                }
            }, 800);
            <?php endif; ?>
        },
        
        async submitOrder() {
            this.loading = true;
            this.error = '';
            
            try {
                const res = await fetch('/orders/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        game_id: <?php echo e($game->id ?? 0); ?>,
                        package_id: this.selectedPackage,
                        user_id: this.form.user_id,
                        server_id: this.form.server_id,
                        payment_method: this.form.payment_method
                    })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = '/orders/' + data.order_id;
                } else {
                    this.error = data.message || 'Failed to create order';
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/user/games/show.blade.php ENDPATH**/ ?>
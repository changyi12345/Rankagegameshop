

<?php $__env->startSection('title', 'Login - RanKage Game Shop'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-md">
    <!-- Logo Section -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center mx-auto mb-4 shadow-xl">
            <span class="text-5xl">🎮</span>
        </div>
        <?php
            $siteName = \App\Models\Setting::get('site_name', 'RanKage');
            $siteTagline = \App\Models\Setting::get('site_tagline', 'Game Shop');
        ?>
        <h1 class="text-3xl font-bold text-light-text mb-2"><?php echo e($siteName); ?></h1>
        <p class="text-gray-400 mb-1"><?php echo e($siteTagline); ?></p>
        <div class="w-16 h-1 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full"></div>
    </div>

    <!-- Login Card -->
    <div class="card">
        <h2 class="text-2xl font-bold text-light-text mb-6 text-center">Login</h2>
        
        <div x-data="loginData()">
            <!-- Login Method Tabs -->
            <div class="flex space-x-2 mb-6 bg-dark-base rounded-xl p-1">
                <button type="button" 
                        @click="loginMethod = 'phone'"
                        :class="loginMethod === 'phone' ? 'bg-primary text-white' : 'text-gray-400'"
                        class="flex-1 py-2 px-4 rounded-lg font-semibold text-sm transition-all">
                    Phone
                </button>
                <button type="button" 
                        @click="loginMethod = 'email'"
                        :class="loginMethod === 'email' ? 'bg-primary text-white' : 'text-gray-400'"
                        class="flex-1 py-2 px-4 rounded-lg font-semibold text-sm transition-all">
                    Email
                </button>
            </div>

            <!-- Phone Login Form -->
            <form x-show="loginMethod === 'phone'" @submit.prevent="login" class="space-y-5">
                <div>
                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <input type="tel" 
                                   x-model="form.phone" 
                                   required 
                                   class="input-field pl-12" 
                                   placeholder="09123456789"
                                   maxlength="11"
                                   pattern="[0-9]*"
                                   inputmode="numeric">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Myanmar phone number</p>
                    </div>

                    <!-- OTP Code (shown after phone verification) -->
                    <div x-show="showOTP" x-transition class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">OTP Code</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input type="text" 
                                       x-model="form.otp" 
                                       required 
                                       class="input-field pl-12 text-center text-2xl tracking-widest" 
                                       placeholder="000000"
                                       maxlength="6"
                                       pattern="[0-9]*"
                                       inputmode="numeric">
                            </div>
                            <p class="text-xs text-gray-500 mt-1 text-center">
                                <span x-text="timer > 0 ? 'Resend OTP in ' + timer + 's' : 'Didn\'t receive? '"></span>
                                <a x-show="timer === 0" @click="sendOTP()" class="text-primary hover:underline cursor-pointer">Resend</a>
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="btn-primary w-full py-4 text-base font-semibold"
                            :disabled="loading">
                        <span x-show="!loading" class="flex items-center justify-center">
                            <span x-text="showOTP ? 'Verify OTP' : 'Send OTP'"></span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="showOTP ? 'Verifying...' : 'Sending...'"></span>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Email Login Form -->
            <form x-show="loginMethod === 'email'" @submit.prevent="loginWithEmail" class="space-y-5">
                <div>
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input type="email" 
                                   x-model="emailForm.email" 
                                   required 
                                   class="input-field pl-12" 
                                   placeholder="your@email.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" 
                                   x-model="emailForm.password" 
                                   required 
                                   class="input-field pl-12" 
                                   placeholder="Enter your password">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" 
                               x-model="emailForm.remember" 
                               id="remember"
                               class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                        <label for="remember" class="ml-2 text-sm text-gray-300">Remember me</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="btn-primary w-full py-4 text-base font-semibold"
                            :disabled="loading">
                        <span x-show="!loading" class="flex items-center justify-center">
                            Login
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Logging in...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Error Message -->
            <div x-show="error" 
                 x-transition
                 class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm flex items-center space-x-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-text="error"></span>
            </div>

            <!-- Success Message (OTP Sent) -->
            <div x-show="otpSent && loginMethod === 'phone'" 
                 x-transition
                 class="bg-secondary/10 border border-secondary/30 rounded-xl p-4 text-secondary text-sm flex items-center space-x-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>OTP sent to <span x-text="form.phone"></span></span>
            </div>

            <!-- Divider -->
            <div class="mt-6 pt-6 border-t border-gray-800">
                <p class="text-center text-sm text-gray-400 mb-4">Or continue with</p>
                
                <!-- Telegram Login Button -->
                <?php if(config('services.telegram.bot_username')): ?>
                <script async src="https://telegram.org/js/telegram-widget.js?22" 
                        data-telegram-login="<?php echo e(config('services.telegram.bot_username')); ?>" 
                        data-size="large" 
                        data-onauth="onTelegramAuth(user)" 
                        data-request-access="write"></script>
                <?php else: ?>
                <div class="bg-gray-800 rounded-xl p-4 text-center text-gray-400 text-sm">
                    Telegram login is not configured. Please set TELEGRAM_BOT_USERNAME in .env
                </div>
                <?php endif; ?>
            </div>

            <!-- Register Link -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-400">
                    Don't have an account? 
                    <a href="/register" class="text-primary hover:underline font-semibold">Register</a>
                </p>
            </div>

            <!-- Guest Browse -->
            <div class="mt-4 text-center">
                <a href="/" class="text-sm text-gray-400 hover:text-primary transition-colors">
                    Continue as Guest (Browse Only)
                </a>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function loginData() {
    return {
        loginMethod: 'phone',
        form: {
            phone: '',
            otp: ''
        },
        emailForm: {
            email: '',
            password: '',
            remember: false
        },
        showOTP: false,
        otpSent: false,
        loading: false,
        error: '',
        timer: 0,
        
        async login() {
            this.loading = true;
            this.error = '';
            
            if (!this.showOTP) {
                // Send OTP
                await this.sendOTP();
            } else {
                // Verify OTP
                await this.verifyOTP();
            }
        },

        async loginWithEmail() {
            this.loading = true;
            this.error = '';
            
            try {
                const res = await fetch('/auth/login-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.emailForm)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = data.redirect || '/';
                } else {
                    this.error = data.message || 'Invalid email or password';
                }
            } catch (e) {
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        },
        
        async sendOTP() {
            try {
                const res = await fetch('/auth/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ phone: this.form.phone })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    this.showOTP = true;
                    this.otpSent = true;
                    this.startTimer();
                } else {
                    this.error = data.message || 'Failed to send OTP';
                }
            } catch (e) {
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        },
        
        async verifyOTP() {
            try {
                const res = await fetch('/auth/verify-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        phone: this.form.phone,
                        otp: this.form.otp
                    })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = data.redirect || '/';
                } else {
                    this.error = data.message || 'Invalid OTP code';
                }
            } catch (e) {
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        },
        
        startTimer() {
            this.timer = 60;
            const interval = setInterval(() => {
                this.timer--;
                if (this.timer <= 0) {
                    clearInterval(interval);
                }
            }, 1000);
        }
    }
}

// Telegram Login Callback
async function onTelegramAuth(user) {
    try {
        const res = await fetch('/auth/telegram', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(user)
        });
        
        const data = await res.json();
        
        if (data.success) {
            window.location.href = data.redirect || '/';
        } else {
            alert('Telegram login failed: ' + (data.message || 'Unknown error'));
        }
    } catch (e) {
        alert('An error occurred during Telegram login. Please try again.');
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/user/login.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'Login - RanKage Game Shop'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-md">
    <!-- Logo Section -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center mx-auto mb-4 shadow-xl">
            <span class="text-5xl">🎮</span>
        </div>
        <h1 class="text-3xl font-bold text-light-text mb-2">RanKage</h1>
        <p class="text-gray-400 mb-1">Game Shop</p>
        <div class="w-16 h-1 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full"></div>
    </div>

    <!-- Login Card -->
    <div class="card">
        <h2 class="text-2xl font-bold text-light-text mb-6 text-center">Login</h2>
        
        <div x-data="loginData()">
            <form @submit.prevent="login">
                <div class="space-y-5">
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
                    <div x-show="otpSent" 
                         x-transition
                         class="bg-secondary/10 border border-secondary/30 rounded-xl p-4 text-secondary text-sm flex items-center space-x-2">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>OTP sent to <span x-text="form.phone"></span></span>
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

            <!-- Divider -->
            <div class="mt-6 pt-6 border-t border-gray-800">
                <p class="text-center text-sm text-gray-400 mb-4">Or continue with</p>
                
                <!-- Telegram Login Button -->
                <a href="/auth/telegram" class="w-full flex items-center justify-center space-x-3 bg-[#0088cc] hover:bg-[#006699] text-white rounded-xl py-3 px-4 font-semibold transition-all">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.165 1.657-.878 5.686-1.241 7.543-.171.872-.508 1.161-.835 1.19-.713.056-1.253-.47-1.942-.922-1.077-.722-1.686-1.17-2.731-1.876-1.218-.835-.428-1.293.266-2.043.182-.195 3.247-2.978 3.307-3.23.007-.032.014-.154-.056-.213-.07-.06-.173-.04-.248-.024-.106.023-1.789 1.14-5.058 3.347-.479.33-.913.49-1.302.482-.428-.008-1.252-.242-1.865-.44-.752-.243-1.349-.374-1.297-.789.027-.216.325-.437.894-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635.099-.002.321.023.465.14.118.095.151.223.167.312.016.09.036.297.02.458z"/>
                    </svg>
                    <span>Telegram</span>
                </a>
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
        form: {
            phone: '',
            otp: ''
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
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/user/login.blade.php ENDPATH**/ ?>
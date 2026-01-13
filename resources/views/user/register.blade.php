@extends('layouts.user')

@section('title', 'Register - RanKage Game Shop')

@section('content')
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

    <!-- Register Card -->
    <div class="card">
        <h2 class="text-2xl font-bold text-light-text mb-6 text-center">Create Account</h2>
        
        <div x-data="registerData()">
            <form @submit.prevent="register">
                <div class="space-y-5">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   x-model="form.name" 
                                   required 
                                   class="input-field pl-12" 
                                   placeholder="Your Name">
                        </div>
                    </div>

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
                                   x-model="form.email" 
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
                                   x-model="form.password" 
                                   required 
                                   minlength="8"
                                   class="input-field pl-12" 
                                   placeholder="Minimum 8 characters">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" 
                                   x-model="form.password_confirmation" 
                                   required 
                                   class="input-field pl-12" 
                                   placeholder="Confirm your password">
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

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="btn-primary w-full py-4 text-base font-semibold"
                            :disabled="loading">
                        <span x-show="!loading" class="flex items-center justify-center">
                            Create Account
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating Account...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="mt-6 pt-6 border-t border-gray-800">
                <p class="text-center text-sm text-gray-400 mb-4">Or continue with</p>
                
                <!-- Telegram Login Button -->
                @if(config('services.telegram.bot_username'))
                <script async src="https://telegram.org/js/telegram-widget.js?22" 
                        data-telegram-login="{{ config('services.telegram.bot_username') }}" 
                        data-size="large" 
                        data-onauth="onTelegramAuth(user)" 
                        data-request-access="write"></script>
                @else
                <div class="bg-gray-800 rounded-xl p-4 text-center text-gray-400 text-sm">
                    Telegram login is not configured. Please set TELEGRAM_BOT_USERNAME in .env
                </div>
                @endif
            </div>

            <!-- Login Link -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-400">
                    Already have an account? 
                    <a href="/login" class="text-primary hover:underline font-semibold">Login</a>
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function registerData() {
    return {
        form: {
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        },
        loading: false,
        error: '',
        
        async register() {
            this.loading = true;
            this.error = '';
            
            // Validate passwords match
            if (this.form.password !== this.form.password_confirmation) {
                this.error = 'Passwords do not match';
                this.loading = false;
                return;
            }

            // Validate password length
            if (this.form.password.length < 8) {
                this.error = 'Password must be at least 8 characters';
                this.loading = false;
                return;
            }
            
            try {
                const res = await fetch('/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = data.redirect || '/';
                } else {
                    if (data.errors) {
                        // Handle validation errors
                        const firstError = Object.values(data.errors)[0];
                        this.error = Array.isArray(firstError) ? firstError[0] : firstError;
                    } else {
                        this.error = data.message || 'Registration failed';
                    }
                }
            } catch (e) {
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
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
@endpush
@endsection

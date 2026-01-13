@extends('layouts.user')

@section('title', 'Profile - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-2xl">
    <h1 class="text-2xl font-bold text-light-text mb-6 flex items-center">
        <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
        My Profile
    </h1>

    <div x-data="profileData()">
        <!-- Profile Card -->
        <div class="card mb-6">
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                    <span class="text-4xl">👤</span>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-light-text">{{ auth()->user()->name ?? 'User' }}</h2>
                    <p class="text-gray-400 text-sm">{{ auth()->user()->phone ?? 'No phone' }}</p>
                    @if(auth()->user()->telegram_id)
                        <p class="text-gray-400 text-xs mt-1">Telegram: @{{ auth()->user()->telegram_username ?? auth()->user()->telegram_id }}</p>
                    @endif
                </div>
            </div>

            <!-- Wallet Balance -->
            <div class="bg-gradient-to-r from-dark-base to-dark-card rounded-xl p-4 mb-4">
                <p class="text-gray-400 text-sm mb-1">Wallet Balance</p>
                <p class="text-3xl font-bold text-secondary">{{ number_format(auth()->user()->balance ?? 0) }} <span class="text-lg text-gray-400">Ks</span></p>
            </div>

            <div class="flex gap-3">
                <a href="/wallet" class="btn-primary flex-1 text-center py-3">Top Up Wallet</a>
                <a href="/orders" class="btn-outline flex-1 text-center py-3">View Orders</a>
            </div>
        </div>

        <!-- Account Information -->
        <div class="card mb-6">
            <h2 class="text-xl font-bold text-light-text mb-4">Account Information</h2>
            
            <form @submit.prevent="updateProfile">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Name</label>
                        <input type="text" 
                               x-model="form.name" 
                               class="input-field" 
                               placeholder="Your name"
                               value="{{ auth()->user()->name ?? '' }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Phone Number</label>
                        <input type="tel" 
                               x-model="form.phone" 
                               class="input-field" 
                               placeholder="09123456789"
                               value="{{ auth()->user()->phone ?? '' }}"
                               disabled>
                        <p class="text-xs text-gray-500 mt-1">Phone number cannot be changed</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Email (Optional)</label>
                        <input type="email" 
                               x-model="form.email" 
                               class="input-field" 
                               placeholder="your@email.com"
                               value="{{ auth()->user()->email ?? '' }}">
                    </div>

                    <div x-show="error" 
                         x-transition
                         class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm">
                        <span x-text="error"></span>
                    </div>

                    <div x-show="success" 
                         x-transition
                         class="bg-secondary/10 border border-secondary/30 rounded-xl p-4 text-secondary text-sm">
                        <span>Profile updated successfully!</span>
                    </div>

                    <button type="submit" 
                            class="btn-primary w-full py-3"
                            :disabled="loading">
                        <span x-show="!loading">Update Profile</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="card mb-6">
            <h2 class="text-xl font-bold text-light-text mb-4">Change Password</h2>
            
            <form @submit.prevent="changePassword">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Current Password</label>
                        <input type="password" 
                               x-model="passwordForm.current_password" 
                               class="input-field" 
                               placeholder="Enter current password">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">New Password</label>
                        <input type="password" 
                               x-model="passwordForm.new_password" 
                               class="input-field" 
                               placeholder="Enter new password">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Confirm New Password</label>
                        <input type="password" 
                               x-model="passwordForm.new_password_confirmation" 
                               class="input-field" 
                               placeholder="Confirm new password">
                    </div>

                    <div x-show="passwordError" 
                         x-transition
                         class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm">
                        <span x-text="passwordError"></span>
                    </div>

                    <div x-show="passwordSuccess" 
                         x-transition
                         class="bg-secondary/10 border border-secondary/30 rounded-xl p-4 text-secondary text-sm">
                        <span>Password changed successfully!</span>
                    </div>

                    <button type="submit" 
                            class="btn-outline w-full py-3"
                            :disabled="passwordLoading">
                        <span x-show="!passwordLoading">Change Password</span>
                        <span x-show="passwordLoading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Changing...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Logout -->
        <div class="card">
            <button @click="logout()" class="w-full text-red-400 hover:text-red-300 font-semibold py-3 text-center">
                Logout
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function profileData() {
    return {
        form: {
            name: '{{ auth()->user()->name ?? '' }}',
            phone: '{{ auth()->user()->phone ?? '' }}',
            email: '{{ auth()->user()->email ?? '' }}'
        },
        passwordForm: {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        },
        loading: false,
        passwordLoading: false,
        error: '',
        success: false,
        passwordError: '',
        passwordSuccess: false,
        
        async updateProfile() {
            this.loading = true;
            this.error = '';
            this.success = false;
            
            try {
                const res = await fetch('/profile/update', {
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
                    setTimeout(() => {
                        this.success = false;
                    }, 3000);
                } else {
                    this.error = data.message || 'Failed to update profile';
                }
            } catch (e) {
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        },
        
        async changePassword() {
            this.passwordLoading = true;
            this.passwordError = '';
            this.passwordSuccess = false;
            
            if (this.passwordForm.new_password !== this.passwordForm.new_password_confirmation) {
                this.passwordError = 'New passwords do not match';
                this.passwordLoading = false;
                return;
            }
            
            try {
                const res = await fetch('/profile/change-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.passwordForm)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    this.passwordSuccess = true;
                    this.passwordForm = {
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: ''
                    };
                    setTimeout(() => {
                        this.passwordSuccess = false;
                    }, 3000);
                } else {
                    this.passwordError = data.message || 'Failed to change password';
                }
            } catch (e) {
                this.passwordError = 'An error occurred. Please try again.';
            } finally {
                this.passwordLoading = false;
            }
        },
        
        async logout() {
            if (confirm('Are you sure you want to logout?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';
                form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').content + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
}
</script>
@endpush
@endsection

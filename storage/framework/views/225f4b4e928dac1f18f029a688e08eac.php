

<?php $__env->startSection('title', 'Profile - Admin Panel'); ?>
<?php $__env->startSection('page-title', 'Profile Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl">
    <div x-data="profileData()">
        <!-- Profile Information Card -->
        <div class="card mb-6">
            <h2 class="text-xl font-bold text-light-text mb-4">Profile Information</h2>
            
            <form @submit.prevent="updateProfile">
                <div class="space-y-4">
                    <!-- Avatar Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Profile Picture</label>
                        <div class="flex items-center space-x-6">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center overflow-hidden">
                                    <template x-if="form.avatarPreview">
                                        <img :src="form.avatarPreview" alt="Avatar" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!form.avatarPreview && currentAvatar">
                                        <img :src="currentAvatar" alt="Avatar" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!form.avatarPreview && !currentAvatar">
                                        <span class="text-4xl">👤</span>
                                    </template>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file" 
                                       @change="handleAvatarUpload($event)"
                                       accept="image/*"
                                       class="hidden"
                                       id="avatar-upload"
                                       ref="avatarInput">
                                <label for="avatar-upload" 
                                       class="btn-outline cursor-pointer px-4 py-2 inline-block">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php echo e(auth()->user()->avatar ? 'Change Avatar' : 'Upload Avatar'); ?>

                                </label>
                                <button type="button" 
                                        x-show="form.avatarPreview || currentAvatar"
                                        @click="removeAvatar"
                                        class="ml-2 text-red-400 hover:text-red-300 text-sm">
                                    Remove
                                </button>
                                <p class="text-xs text-gray-500 mt-2">Upload profile picture (JPG, PNG, GIF, WebP - Max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Name <span class="text-red-400">*</span></label>
                        <input type="text" 
                               x-model="form.name" 
                               required 
                               class="input-field" 
                               placeholder="Your Name">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Email</label>
                        <input type="email" 
                               x-model="form.email" 
                               class="input-field" 
                               placeholder="your@email.com">
                    </div>

                    <!-- Phone (Read-only) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Phone</label>
                        <input type="text" 
                               value="<?php echo e(auth()->user()->phone ?? 'N/A'); ?>" 
                               disabled
                               class="input-field bg-dark-base opacity-50 cursor-not-allowed">
                    </div>
                </div>

                <div class="mt-6 flex items-center space-x-4">
                    <button type="submit" 
                            class="btn-primary px-8 py-3"
                            :disabled="loading">
                        <span x-show="!loading">Save Changes</span>
                        <span x-show="loading" class="flex items-center">
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

        <!-- Change Password Card -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">Change Password</h2>
            
            <form @submit.prevent="changePassword">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Current Password <span class="text-red-400">*</span></label>
                        <input type="password" 
                               x-model="passwordForm.current_password" 
                               required 
                               class="input-field" 
                               placeholder="Enter current password">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">New Password <span class="text-red-400">*</span></label>
                        <input type="password" 
                               x-model="passwordForm.new_password" 
                               required 
                               class="input-field" 
                               placeholder="Enter new password">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Confirm New Password <span class="text-red-400">*</span></label>
                        <input type="password" 
                               x-model="passwordForm.new_password_confirmation" 
                               required 
                               class="input-field" 
                               placeholder="Confirm new password">
                    </div>

                    <div x-show="passwordError" 
                         x-transition
                         class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm">
                        <span x-text="passwordError"></span>
                    </div>
                </div>

                <div class="mt-6 flex items-center space-x-4">
                    <button type="submit" 
                            class="btn-primary px-8 py-3"
                            :disabled="passwordLoading">
                        <span x-show="!passwordLoading">Change Password</span>
                        <span x-show="passwordLoading" class="flex items-center">
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
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function profileData() {
    return {
        form: {
            name: '<?php echo e(auth()->user()->name ?? ''); ?>',
            email: '<?php echo e(auth()->user()->email ?? ''); ?>',
            avatar: null,
            avatarPreview: null,
            removeAvatar: false
        },
        currentAvatar: <?php if(auth()->user()->avatar): ?> '<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>' <?php else: ?> null <?php endif; ?>,
        loading: false,
        passwordForm: {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        },
        passwordLoading: false,
        passwordError: '',
        
        handleAvatarUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Image size must be less than 2MB');
                    return;
                }
                this.form.avatar = file;
                this.form.removeAvatar = false; // Reset remove flag when uploading new image
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.avatarPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        removeAvatar() {
            this.form.avatar = null;
            this.form.avatarPreview = null;
            this.currentAvatar = null;
            if (this.$refs.avatarInput) {
                this.$refs.avatarInput.value = '';
            }
            // Mark for removal
            this.form.removeAvatar = true;
        },
        
        async updateProfile() {
            this.loading = true;
            try {
                const formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('email', this.form.email);
                
                if (this.form.removeAvatar) {
                    formData.append('remove_avatar', '1');
                } else if (this.form.avatar) {
                    formData.append('avatar', this.form.avatar);
                }
                
                const res = await fetch('/admin/profile', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await res.json();
                if (data.success) {
                    alert('Profile updated successfully!');
                    // Reset removeAvatar flag
                    this.form.removeAvatar = false;
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to update profile');
                }
            } catch (e) {
                alert('An error occurred');
            } finally {
                this.loading = false;
            }
        },
        
        async changePassword() {
            this.passwordLoading = true;
            this.passwordError = '';
            
            try {
                const res = await fetch('/admin/profile/password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.passwordForm)
                });
                
                const data = await res.json();
                if (data.success) {
                    alert('Password changed successfully!');
                    this.passwordForm = {
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: ''
                    };
                } else {
                    this.passwordError = data.message || 'Failed to change password';
                }
            } catch (e) {
                this.passwordError = 'An error occurred';
            } finally {
                this.passwordLoading = false;
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/admin/profile/index.blade.php ENDPATH**/ ?>
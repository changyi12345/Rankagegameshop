

<?php $__env->startSection('title', 'Promotion Management - Admin Panel'); ?>
<?php $__env->startSection('page-title', 'Promotion Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl">
    <div x-data="promotionFormData()">
        <form @submit.prevent="submitForm">
            <div class="card mb-6">
                <h2 class="text-xl font-bold text-light-text mb-4">Promotion Banner Settings</h2>
                
                <div class="space-y-4">
                    <!-- Enable/Disable -->
                    <div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   x-model="form.enabled" 
                                   class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-300">Enable Promotion Banner</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">Show/hide the promotion banner on home page</p>
                    </div>

                    <!-- Icon -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Icon (Emoji)</label>
                        <input type="text" 
                               x-model="form.icon" 
                               maxlength="2"
                               class="input-field text-2xl text-center" 
                               placeholder="🎉">
                        <p class="text-xs text-gray-500 mt-1">Enter emoji icon (e.g., 🎉, 🎁, ⚡)</p>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Title <span class="text-red-400">*</span></label>
                        <input type="text" 
                               x-model="form.title" 
                               required 
                               class="input-field" 
                               placeholder="Special Promotion!">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Description <span class="text-red-400">*</span></label>
                        <textarea x-model="form.description" 
                                  required 
                                  rows="3"
                                  class="input-field" 
                                  placeholder="Get 10% extra diamonds on Mobile Legends!"></textarea>
                    </div>

                    <!-- Button Text -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Button Text <span class="text-red-400">*</span></label>
                        <input type="text" 
                               x-model="form.button_text" 
                               required 
                               class="input-field" 
                               placeholder="Shop Now">
                    </div>

                    <!-- Button Link -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Button Link <span class="text-red-400">*</span></label>
                        <input type="text" 
                               x-model="form.button_link" 
                               required 
                               class="input-field" 
                               placeholder="/games">
                        <p class="text-xs text-gray-500 mt-1">Enter relative URL (e.g., /games, /games/1)</p>
                    </div>

                    <!-- Preview -->
                    <div class="mt-6 p-4 bg-dark-base rounded-xl border border-dark-border">
                        <p class="text-sm font-semibold text-gray-400 mb-3">Preview:</p>
                        <div class="bg-gradient-to-r from-primary via-primary-light to-secondary p-6 text-white rounded-xl relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                            <div class="relative z-10">
                                <h2 class="text-2xl font-bold mb-2" x-text="form.icon + ' ' + form.title"></h2>
                                <p class="text-white/90 mb-4" x-text="form.description"></p>
                                <button type="button" class="bg-white text-primary px-6 py-2 rounded-xl font-semibold" x-text="form.button_text"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" 
                        class="btn-primary px-8 py-3"
                        :disabled="loading">
                    <span x-show="!loading">Save Promotion</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
                <a href="/admin/dashboard" class="btn-outline px-8 py-3">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function promotionFormData() {
    return {
        form: {
            enabled: <?php echo e($promotion['enabled'] ? 'true' : 'false'); ?>,
            icon: '<?php echo e($promotion['icon'] ?? '🎉'); ?>',
            title: '<?php echo e($promotion['title'] ?? 'Special Promotion!'); ?>',
            description: '<?php echo e($promotion['description'] ?? 'Get 10% extra diamonds on Mobile Legends!'); ?>',
            button_text: '<?php echo e($promotion['button_text'] ?? 'Shop Now'); ?>',
            button_link: '<?php echo e($promotion['button_link'] ?? '/games'); ?>'
        },
        loading: false,
        
        async submitForm() {
            this.loading = true;
            try {
                const res = await fetch('/admin/promotions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });
                const data = await res.json();
                if (data.success) {
                    alert('Promotion updated successfully!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to update promotion');
                }
            } catch (e) {
                alert('An error occurred');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/admin/promotions/index.blade.php ENDPATH**/ ?>
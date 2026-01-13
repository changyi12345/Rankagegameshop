@extends('layouts.admin')

@section('title', 'Add New Game - Admin Panel')
@section('page-title', 'Add New Game')

@section('content')
<div class="max-w-4xl">
    <div x-data="gameFormData()">
        <form @submit.prevent="submitForm">
            <div class="card mb-6">
                <h2 class="text-xl font-bold text-light-text mb-4">Game Information</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Game Name <span class="text-red-400">*</span></label>
                        <input type="text" 
                               x-model="form.name" 
                               required 
                               class="input-field" 
                               placeholder="e.g., Mobile Legends">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Game Icon (Emoji) <span class="text-red-400">*</span></label>
                        <input type="text" 
                               x-model="form.icon" 
                               required 
                               maxlength="2"
                               class="input-field text-2xl text-center" 
                               placeholder="⚔️">
                        <p class="text-xs text-gray-500 mt-1">Enter emoji icon (e.g., ⚔️, 🎯, 🔥)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Game Image</label>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-4">
                                <input type="file" 
                                       @change="handleImageUpload($event)"
                                       accept="image/*"
                                       class="hidden"
                                       id="image-upload"
                                       ref="imageInput">
                                <label for="image-upload" 
                                       class="btn-outline cursor-pointer px-4 py-2 inline-block">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Choose Image
                                </label>
                                <button type="button" 
                                        x-show="form.imagePreview"
                                        @click="removeImage"
                                        class="text-red-400 hover:text-red-300 text-sm">
                                    Remove
                                </button>
                            </div>
                            <div x-show="form.imagePreview" class="mt-2">
                                <img :src="form.imagePreview" 
                                     alt="Preview" 
                                     class="w-32 h-32 object-cover rounded-lg border border-dark-border">
                            </div>
                            <p class="text-xs text-gray-500">Upload game image (JPG, PNG, GIF, WebP - Max 2MB)</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Currency Name <span class="text-red-400">*</span></label>
                        <input type="text" 
                               x-model="form.currency_name" 
                               required 
                               class="input-field" 
                               placeholder="e.g., Diamonds, UC, VP">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Requires Server ID?</label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   x-model="form.requires_server" 
                                   class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-300">Yes, this game requires server ID</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Profit Margin (%)</label>
                        <input type="number" 
                               x-model="form.profit_margin" 
                               min="0" 
                               max="100"
                               step="0.1"
                               class="input-field" 
                               placeholder="10"
                               value="10">
                        <p class="text-xs text-gray-500 mt-1">Default profit margin for packages</p>
                    </div>

                    <div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   x-model="form.is_active" 
                                   checked
                                   class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-300">Active (Enable this game)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" 
                        class="btn-primary px-8 py-3"
                        :disabled="loading">
                    <span x-show="!loading">Create Game</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
                <a href="/admin/games" class="btn-outline px-8 py-3">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function gameFormData() {
    return {
        form: {
            name: '',
            icon: '',
            image: null,
            imagePreview: null,
            currency_name: '',
            requires_server: false,
            profit_margin: 10,
            is_active: true
        },
        loading: false,
        
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Image size must be less than 2MB');
                    return;
                }
                this.form.image = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        removeImage() {
            this.form.image = null;
            this.form.imagePreview = null;
            if (this.$refs.imageInput) {
                this.$refs.imageInput.value = '';
            }
        },
        
        async submitForm() {
            this.loading = true;
            try {
                const formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('icon', this.form.icon);
                formData.append('currency_name', this.form.currency_name);
                formData.append('requires_server', this.form.requires_server ? '1' : '0');
                formData.append('profit_margin', this.form.profit_margin);
                formData.append('is_active', this.form.is_active ? '1' : '0');
                
                if (this.form.image) {
                    formData.append('image', this.form.image);
                }
                
                const res = await fetch('/admin/games', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    window.location.href = '/admin/games';
                } else {
                    alert(data.message || 'Failed to create game');
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
@endpush
@endsection

@extends('layouts.admin')

@section('title', 'Logo Management - Admin Panel')
@section('page-title', 'Logo Management')

@section('content')
<div class="max-w-4xl">
    <div x-data="logoFormData()">
        <form @submit.prevent="submitForm">
            <div class="card mb-6">
                <h2 class="text-xl font-bold text-light-text mb-4">Site Logo Settings</h2>
                
                <div class="space-y-4">
                    <!-- Logo Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Site Logo</label>
                        <div class="flex items-center space-x-6">
                            <div class="relative">
                                <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center overflow-hidden border-2 border-dark-border">
                                    <template x-if="form.logoPreview">
                                        <img :src="form.logoPreview" alt="Logo" class="w-full h-full object-contain p-2">
                                    </template>
                                    <template x-if="!form.logoPreview && currentLogo">
                                        <img :src="currentLogo" alt="Logo" class="w-full h-full object-contain p-2">
                                    </template>
                                    <template x-if="!form.logoPreview && !currentLogo">
                                        <div class="text-center p-4">
                                            <span class="text-5xl block mb-2">🎮</span>
                                            <span class="text-xs text-gray-400">Default Logo</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file" 
                                       @change="handleLogoUpload($event)"
                                       accept="image/*"
                                       class="hidden"
                                       id="logo-upload"
                                       ref="logoInput">
                                <label for="logo-upload" 
                                       class="btn-outline cursor-pointer px-4 py-2 inline-block">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $logo ? 'Change Logo' : 'Upload Logo' }}
                                </label>
                                <button type="button" 
                                        x-show="form.logoPreview || currentLogo"
                                        @click="removeLogo"
                                        class="ml-2 text-red-400 hover:text-red-300 text-sm">
                                    Remove
                                </button>
                                <p class="text-xs text-gray-500 mt-2">Upload site logo (JPG, PNG, GIF, WebP, SVG - Max 2MB)</p>
                                <p class="text-xs text-gray-500 mt-1">Recommended size: 200x200px or square aspect ratio</p>
                            </div>
                        </div>
                    </div>

                    <!-- Site Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Site Name <span class="text-red-400">*</span></label>
                        <input type="text" 
                               x-model="form.siteName" 
                               required 
                               class="input-field" 
                               placeholder="RanKage">
                        <p class="text-xs text-gray-500 mt-1">The main brand name displayed next to the logo</p>
                    </div>

                    <!-- Site Tagline -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Site Tagline</label>
                        <input type="text" 
                               x-model="form.siteTagline" 
                               class="input-field" 
                               placeholder="Game Shop">
                        <p class="text-xs text-gray-500 mt-1">Optional tagline displayed below the site name</p>
                    </div>

                    <!-- Preview Section -->
                    <div class="mt-6 p-4 bg-dark-base rounded-xl border border-dark-border">
                        <h3 class="text-sm font-semibold text-gray-300 mb-3">Preview</h3>
                        <div class="bg-dark-card p-4 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <template x-if="form.logoPreview">
                                        <img :src="form.logoPreview" alt="Logo" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!form.logoPreview && currentLogo">
                                        <img :src="currentLogo" alt="Logo" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!form.logoPreview && !currentLogo">
                                        <span class="text-2xl">🎮</span>
                                    </template>
                                </div>
                                <div>
                                    <h1 class="text-lg font-bold text-light-text" x-text="form.siteName || 'RanKage'"></h1>
                                    <p class="text-xs text-gray-400" x-text="form.siteTagline || 'Game Shop'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center space-x-4">
                    <button type="submit" 
                            class="btn-primary px-8 py-3"
                            :disabled="loading">
                        <span x-show="!loading">Save Logo</span>
                        <span x-show="loading" class="flex items-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function logoFormData() {
    return {
        form: {
            logo: null,
            logoPreview: null,
            removeLogo: false,
            siteName: '{{ $siteName ?? 'RanKage' }}',
            siteTagline: '{{ $siteTagline ?? 'Game Shop' }}'
        },
        currentLogo: @if($logo) '{{ asset('storage/' . $logo) }}' @else null @endif,
        loading: false,
        
        handleLogoUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Image size must be less than 2MB');
                    return;
                }
                this.form.logo = file;
                this.form.removeLogo = false; // Reset remove flag when uploading new image
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.logoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        removeLogo() {
            this.form.logo = null;
            this.form.logoPreview = null;
            this.currentLogo = null;
            if (this.$refs.logoInput) {
                this.$refs.logoInput.value = '';
            }
            // Mark for removal
            this.form.removeLogo = true;
        },
        
        async submitForm() {
            this.loading = true;
            try {
                const formData = new FormData();
                
                // Add site name and tagline
                formData.append('site_name', this.form.siteName);
                formData.append('site_tagline', this.form.siteTagline || '');
                
                if (this.form.removeLogo) {
                    formData.append('remove_logo', '1');
                } else if (this.form.logo) {
                    formData.append('logo', this.form.logo);
                }
                
                const res = await fetch('/admin/logo', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await res.json();
                if (data.success) {
                    alert('Logo settings updated successfully!');
                    // Reset removeLogo flag
                    this.form.removeLogo = false;
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to update logo settings');
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

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
            currency_name: '',
            requires_server: false,
            profit_margin: 10,
            is_active: true
        },
        loading: false,
        
        async submitForm() {
            this.loading = true;
            try {
                const res = await fetch('/admin/games', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
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

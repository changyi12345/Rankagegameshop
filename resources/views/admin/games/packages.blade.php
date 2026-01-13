@extends('layouts.admin')

@section('title', 'Packages - ' . ($game->name ?? 'Game') . ' - Admin Panel')
@section('page-title', 'Packages: ' . ($game->name ?? 'Game'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="/admin/games" class="text-gray-400 hover:text-primary text-sm mb-2 inline-block">← Back to Games</a>
            <h2 class="text-2xl font-bold text-light-text">Manage Packages</h2>
        </div>
        <div class="flex space-x-2">
            <button onclick="fetchG2BulkPackages()" class="btn-outline px-6 py-3">🔄 Fetch from G2Bulk</button>
            <button onclick="showAddPackage()" class="btn-primary px-6 py-3">Add Package</button>
        </div>
    </div>

    <!-- G2Bulk Packages Section -->
    @if(!empty($g2bulkCatalogues))
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-light-text">G2Bulk Packages ({{ count($g2bulkCatalogues) }})</h3>
            <span class="badge badge-info">Auto-fetched from G2Bulk API</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-border">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Package Name</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Price (USD)</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Price (Ks)</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($g2bulkCatalogues as $catalogue)
                    @php
                        $priceInKs = ($catalogue['amount'] ?? 0) * ($exchangeRate ?? 2100);
                        $isImported = collect($packages ?? [])->contains('name', $catalogue['name'] ?? '');
                    @endphp
                    <tr class="border-b border-dark-border hover:bg-dark-base transition-colors">
                        <td class="py-3 px-4">
                            <span class="text-light-text font-semibold">{{ $catalogue['name'] ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-primary font-semibold">${{ number_format($catalogue['amount'] ?? 0, 2) }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-secondary font-semibold">{{ number_format($priceInKs) }} Ks</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($isImported)
                                <span class="badge badge-success">Imported</span>
                            @else
                                <span class="badge badge-warning">Not Imported</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($isImported)
                                <span class="text-gray-500 text-sm">Already imported</span>
                            @else
                                <button onclick="importG2BulkPackage({{ $catalogue['id'] ?? 0 }}, '{{ $catalogue['name'] ?? '' }}', {{ $catalogue['amount'] ?? 0 }})" 
                                        class="text-primary hover:text-primary-light text-sm">Import</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Local Packages List -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-light-text">Local Packages ({{ count($packages ?? []) }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-border">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Package Name</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Currency Amount</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Price (Ks)</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Bonus</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages ?? [] as $package)
                    <tr class="border-b border-dark-border hover:bg-dark-base transition-colors">
                        <td class="py-3 px-4">
                            <span class="text-light-text font-semibold">{{ $package->name }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-primary font-semibold">{{ number_format($package->currency_amount) }}</span>
                            <span class="text-gray-400 text-sm ml-1">{{ $game->currency_name ?? '' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-secondary font-semibold">{{ number_format($package->price) }} Ks</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($package->bonus > 0)
                                <span class="badge badge-success">+{{ $package->bonus }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($package->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-2">
                                <button onclick="editPackage({{ $package->id }})" class="text-primary hover:text-primary-light text-sm">Edit</button>
                                <button onclick="deletePackage({{ $package->id }})" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">No packages found. Add your first package or import from G2Bulk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Package Modal -->
<div id="packageModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closePackageModal()"></div>
        <div class="relative bg-dark-card rounded-2xl p-6 max-w-md w-full border border-dark-border">
            <h3 class="text-xl font-bold text-light-text mb-4" id="modalTitle">Add Package</h3>
            <div x-data="packageFormData()" id="packageFormContainer">
                <form @submit.prevent="submitPackage">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Package Name</label>
                            <input type="text" 
                                   x-model="form.name" 
                                   required 
                                   class="input-field" 
                                   placeholder="e.g., 100 Diamonds">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Currency Amount</label>
                            <input type="number" 
                                   x-model="form.currency_amount" 
                                   required 
                                   min="1"
                                   class="input-field" 
                                   placeholder="100">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Price (Myanmar Kyat - Ks) <span class="text-red-400">*</span>
                            </label>
                            <input type="number" 
                                   x-model="form.price" 
                                   required 
                                   min="1"
                                   step="0.01"
                                   class="input-field" 
                                   placeholder="1000">
                            <p class="text-xs text-gray-500 mt-1">Enter the price in Myanmar Kyat (Ks)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Bonus Amount (Optional)</label>
                            <input type="number" 
                                   x-model="form.bonus" 
                                   min="0"
                                   class="input-field" 
                                   placeholder="0">
                        </div>
                        <div>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       x-model="form.is_active" 
                                       :checked="form.is_active"
                                       class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                                <span class="ml-2 text-sm text-gray-300">Active</span>
                            </label>
                        </div>
                        <div x-show="error" class="bg-red-500/10 border border-red-500/30 rounded-xl p-3 text-red-400 text-sm">
                            <span x-text="error"></span>
                        </div>
                        <div x-show="success" class="bg-green-500/10 border border-green-500/30 rounded-xl p-3 text-green-400 text-sm">
                            <span x-text="success"></span>
                        </div>
                        <div class="flex space-x-3 pt-4">
                            <button type="submit" 
                                    class="btn-primary flex-1 py-3"
                                    :disabled="loading">
                                <span x-show="!loading">Save</span>
                                <span x-show="loading" class="flex items-center justify-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                            <button type="button" onclick="closePackageModal()" class="btn-outline flex-1 py-3">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPackageId = null;

window.currentPackageId = null;

function showAddPackage() {
    window.currentPackageId = null;
    document.getElementById('packageModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Add Package';
    
    // Reset form using Alpine component
    setTimeout(() => {
        const container = document.getElementById('packageFormContainer');
        if (container && container._x_dataStack && container._x_dataStack[0]) {
            const formData = container._x_dataStack[0];
            formData.form = {
                name: '',
                currency_amount: '',
                price: '',
                bonus: 0,
                is_active: true
            };
            formData.error = '';
            formData.success = '';
        }
    }, 100);
}

function closePackageModal() {
    document.getElementById('packageModal').classList.add('hidden');
    window.currentPackageId = null;
}

async function editPackage(packageId) {
    window.currentPackageId = packageId;
    document.getElementById('packageModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Edit Package';
    
    try {
        const res = await fetch(`/admin/games/{{ $game->id ?? 0 }}/packages/${packageId}`);
        const data = await res.json();
        
        if (data.success && data.package) {
            const pkg = data.package;
            
            // Update form using Alpine component
            setTimeout(() => {
                const container = document.getElementById('packageFormContainer');
                if (container && container._x_dataStack && container._x_dataStack[0]) {
                    const formData = container._x_dataStack[0];
                    formData.form = {
                        name: pkg.name || '',
                        currency_amount: pkg.currency_amount || '',
                        price: pkg.price || '',
                        bonus: pkg.bonus || 0,
                        is_active: pkg.is_active ?? true
                    };
                    formData.error = '';
                    formData.success = '';
                }
            }, 100);
        } else {
            alert('Failed to load package data');
        }
    } catch (e) {
        alert('Error loading package: ' + e.message);
    }
}

function deletePackage(packageId) {
    if (confirm('Delete this package?')) {
        fetch(`/admin/games/{{ $game->id ?? 0 }}/packages/${packageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

async function fetchG2BulkPackages() {
    if (!confirm('Fetch packages from G2Bulk API? This will refresh the G2Bulk packages list.')) {
        return;
    }
    
    try {
        const res = await fetch('/admin/games/{{ $game->id ?? 0 }}/packages/fetch-g2bulk');
        const data = await res.json();
        
        if (data.success) {
            alert('✅ Packages fetched successfully! Refreshing page...');
            location.reload();
        } else {
            alert('❌ Failed to fetch packages: ' + (data.message || 'Unknown error'));
        }
    } catch (e) {
        alert('❌ Error: ' + e.message);
    }
}

async function importG2BulkPackage(catalogueId, catalogueName, catalogueAmount) {
    if (!confirm(`Import "${catalogueName}" package?\n\nPrice: $${catalogueAmount.toFixed(2)}\n\nThis will create a new package in your local database.`)) {
        return;
    }
    
    try {
        const res = await fetch('/admin/games/{{ $game->id ?? 0 }}/packages/import-g2bulk', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                catalogue_id: catalogueId,
                catalogue_name: catalogueName,
                catalogue_amount: catalogueAmount
            })
        });
        
        const data = await res.json();
        
        if (data.success) {
            alert('✅ Package imported successfully!');
            location.reload();
        } else {
            alert('❌ Failed to import package: ' + (data.message || 'Unknown error'));
        }
    } catch (e) {
        alert('❌ Error: ' + e.message);
    }
}

function packageFormData() {
    return {
        form: {
            name: '',
            currency_amount: '',
            price: '',
            bonus: 0,
            is_active: true
        },
        loading: false,
        error: '',
        success: '',
        async submitPackage() {
            this.loading = true;
            this.error = '';
            this.success = '';
            
            try {
                // Get currentPackageId from global scope
                const packageId = window.currentPackageId || null;
                const url = packageId 
                    ? `/admin/games/{{ $game->id ?? 0 }}/packages/${packageId}`
                    : '/admin/games/{{ $game->id ?? 0 }}/packages';
                
                const method = packageId ? 'PUT' : 'POST';
                
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    this.success = data.message || (packageId ? 'Package updated successfully!' : 'Package created successfully!');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    this.error = data.message || 'Failed to save package';
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
@endpush
@endsection

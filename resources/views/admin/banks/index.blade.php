@extends('layouts.admin')

@section('title', 'Bank Management - Admin Panel')
@section('page-title', 'Bank Management')

@section('content')
<div class="space-y-6">
    <!-- Payment Methods Toggle -->
    <div class="card">
        <h2 class="text-xl font-bold text-light-text mb-4">Payment Methods</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center justify-between p-4 bg-dark-base rounded-xl">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">📱</span>
                    <span class="text-light-text font-semibold">WavePay</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer" onchange="togglePaymentMethod('wavepay', this.checked)">
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
            <div class="flex items-center justify-between p-4 bg-dark-base rounded-xl">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">💳</span>
                    <span class="text-light-text font-semibold">KBZ Pay</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer" onchange="togglePaymentMethod('kpay', this.checked)">
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
            <div class="flex items-center justify-between p-4 bg-dark-base rounded-xl">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">🏦</span>
                    <span class="text-light-text font-semibold">Manual Transfer</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer" onchange="togglePaymentMethod('manual', this.checked)">
                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
        </div>
    </div>

    <!-- Bank Accounts Overview -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-light-text">Bank Accounts</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse(\App\Models\Bank::active()->ordered()->get() as $bank)
            <div class="bg-dark-base rounded-xl p-4 border border-dark-border">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-light-text">{{ $bank->bank_name }}</h3>
                    @if($bank->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </div>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-gray-400">Account Name</p>
                        <p class="text-light-text font-semibold">{{ $bank->account_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Account Number</p>
                        <p class="text-light-text font-semibold font-mono">{{ $bank->account_number }}</p>
                    </div>
                    @if($bank->qr_code)
                    <div>
                        <p class="text-gray-400 mb-2">QR Code</p>
                        <img src="{{ asset('storage/' . $bank->qr_code) }}" alt="QR Code" class="w-32 h-32 object-contain bg-white rounded-lg">
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-8 text-gray-400">
                <p>No bank accounts added yet. Add your first bank below.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Add Bank Button -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-light-text">Manage Banks</h1>
        <div class="flex space-x-3">
            <button onclick="openAddKPayModal()" class="btn-secondary px-6 py-3">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Quick Add KPay
            </button>
            <button onclick="openAddModal()" class="btn-primary px-6 py-3">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Bank
            </button>
        </div>
    </div>

    <!-- Banks List -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-border">
                        <th class="text-left p-4 text-gray-400 font-semibold">Bank Name</th>
                        <th class="text-left p-4 text-gray-400 font-semibold">Account Name</th>
                        <th class="text-left p-4 text-gray-400 font-semibold">Account Number</th>
                        <th class="text-left p-4 text-gray-400 font-semibold">QR Code</th>
                        <th class="text-left p-4 text-gray-400 font-semibold">Sort Order</th>
                        <th class="text-left p-4 text-gray-400 font-semibold">Status</th>
                        <th class="text-right p-4 text-gray-400 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banks as $bank)
                    <tr class="border-b border-dark-border hover:bg-dark-base transition-colors">
                        <td class="p-4 text-light-text font-semibold">{{ $bank->bank_name }}</td>
                        <td class="p-4 text-light-text">{{ $bank->account_name }}</td>
                        <td class="p-4 text-light-text font-mono">{{ $bank->account_number }}</td>
                        <td class="p-4">
                            @if($bank->qr_code)
                                <img src="{{ asset('storage/' . $bank->qr_code) }}" alt="QR Code" class="w-16 h-16 object-contain bg-white rounded">
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-light-text">{{ $bank->sort_order }}</td>
                        <td class="p-4">
                            @if($bank->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-end space-x-2">
                                <button onclick="toggleBank({{ $bank->id }})" class="text-primary hover:text-primary-light text-sm">
                                    {{ $bank->is_active ? 'Disable' : 'Enable' }}
                                </button>
                                <button onclick="openEditModal({{ $bank->id }})" class="text-secondary hover:text-secondary-light text-sm">Edit</button>
                                <button onclick="deleteBank({{ $bank->id }})" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <p>No banks added yet. Click "Add Bank" to get started.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Bank Modal -->
<div id="bankModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-dark-card rounded-2xl p-6 max-w-md w-full border border-dark-border">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-light-text" id="modalTitle">Add Bank</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-light-text">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="bankForm" onsubmit="saveBank(event)" enctype="multipart/form-data">
            <input type="hidden" id="bankId" name="id">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Bank Name *</label>
                    <select id="bankName" name="bank_name" required class="input-field" onchange="handleBankNameChange()">
                        <option value="">Select Bank</option>
                        <option value="KPay">KPay</option>
                        <option value="KBZ Bank">KBZ Bank</option>
                        <option value="CB Bank">CB Bank</option>
                        <option value="AYA Bank">AYA Bank</option>
                        <option value="UAB Bank">UAB Bank</option>
                        <option value="WavePay">WavePay</option>
                        <option value="Other">Other (Custom)</option>
                    </select>
                    <input type="text" id="bankNameCustom" name="bank_name_custom" class="input-field mt-2 hidden" placeholder="Enter custom bank name">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Account Name *</label>
                    <input type="text" id="accountName" name="account_name" required class="input-field" placeholder="Account holder name">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Account Number *</label>
                    <input type="text" id="accountNumber" name="account_number" required class="input-field" placeholder="Account number">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">QR Code</label>
                    <input type="file" id="qrCode" name="qr_code" accept="image/*" class="input-field">
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 2MB</p>
                    <div id="qrPreview" class="mt-2 hidden">
                        <img id="qrPreviewImg" src="" alt="QR Preview" class="w-32 h-32 object-contain bg-white rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Sort Order</label>
                    <input type="number" id="sortOrder" name="sort_order" min="0" value="0" class="input-field" placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="isActive" name="is_active" checked class="w-4 h-4 text-primary bg-dark-base border-dark-border rounded focus:ring-primary">
                    <label for="isActive" class="ml-2 text-sm text-gray-300">Active</label>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="btn-primary flex-1">Save</button>
                    <button type="button" onclick="closeModal()" class="btn-outline flex-1">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentBankId = null;

function togglePaymentMethod(method, enabled) {
    fetch('/admin/payments/toggle-method', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ method, enabled })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Payment method updated successfully', 'success');
        } else {
            showNotification('Failed to update payment method', 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('An error occurred', 'error');
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-2xl ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function openAddModal() {
    currentBankId = null;
    document.getElementById('modalTitle').textContent = 'Add Bank';
    document.getElementById('bankForm').reset();
    document.getElementById('bankId').value = '';
    document.getElementById('qrPreview').classList.add('hidden');
    document.getElementById('bankNameCustom').classList.add('hidden');
    document.getElementById('bankModal').classList.remove('hidden');
}

function openAddKPayModal() {
    currentBankId = null;
    document.getElementById('modalTitle').textContent = 'Add KPay Account';
    document.getElementById('bankForm').reset();
    document.getElementById('bankId').value = '';
    document.getElementById('qrPreview').classList.add('hidden');
    
    // Pre-fill KPay details
    document.getElementById('bankName').value = 'KPay';
    document.getElementById('bankNameCustom').classList.add('hidden');
    document.getElementById('isActive').checked = true;
    
    document.getElementById('bankModal').classList.remove('hidden');
}

function handleBankNameChange() {
    const bankNameSelect = document.getElementById('bankName');
    const customInput = document.getElementById('bankNameCustom');
    
    if (bankNameSelect.value === 'Other') {
        customInput.classList.remove('hidden');
        customInput.required = true;
    } else {
        customInput.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
    }
}

function openEditModal(id) {
    currentBankId = id;
    document.getElementById('modalTitle').textContent = 'Edit Bank';
    
    // Fetch bank data
    fetch(`/admin/banks/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('bankId').value = data.id;
            
            // Check if bank name is in the dropdown options
            const bankNameSelect = document.getElementById('bankName');
            const customInput = document.getElementById('bankNameCustom');
            const bankName = data.bank_name;
            
            // Check if it's a predefined bank
            const predefinedBanks = ['KPay', 'KBZ Bank', 'CB Bank', 'AYA Bank', 'UAB Bank', 'WavePay'];
            if (predefinedBanks.includes(bankName)) {
                bankNameSelect.value = bankName;
                customInput.classList.add('hidden');
            } else {
                bankNameSelect.value = 'Other';
                customInput.value = bankName;
                customInput.classList.remove('hidden');
            }
            
            document.getElementById('accountName').value = data.account_name;
            document.getElementById('accountNumber').value = data.account_number;
            document.getElementById('sortOrder').value = data.sort_order;
            document.getElementById('isActive').checked = data.is_active;
            
            if (data.qr_code) {
                document.getElementById('qrPreviewImg').src = `/storage/${data.qr_code}`;
                document.getElementById('qrPreview').classList.remove('hidden');
            } else {
                document.getElementById('qrPreview').classList.add('hidden');
            }
            
            document.getElementById('bankModal').classList.remove('hidden');
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Failed to load bank data');
        });
}

function closeModal() {
    document.getElementById('bankModal').classList.add('hidden');
    currentBankId = null;
}

// QR Code preview
document.getElementById('qrCode').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('qrPreviewImg').src = e.target.result;
            document.getElementById('qrPreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

function saveBank(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const url = currentBankId 
        ? `/admin/banks/${currentBankId}`
        : '/admin/banks';
    
    // Handle bank name - use custom if "Other" is selected
    const bankNameSelect = document.getElementById('bankName');
    const customInput = document.getElementById('bankNameCustom');
    
    if (bankNameSelect.value === 'Other' && customInput.value) {
        formData.set('bank_name', customInput.value);
    } else if (bankNameSelect.value && bankNameSelect.value !== 'Other') {
        formData.set('bank_name', bankNameSelect.value);
    }
    
    // Remove the custom field from formData if not needed
    formData.delete('bank_name_custom');
    
    // Add CSRF token
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    if (currentBankId) {
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Bank saved successfully!');
            location.reload();
        } else {
            alert(data.message || 'Failed to save bank');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred');
    });
}

function toggleBank(id) {
    if (confirm('Toggle bank status?')) {
        fetch(`/admin/banks/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to toggle bank status');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('An error occurred');
        });
    }
}

function deleteBank(id) {
    if (confirm('Are you sure you want to delete this bank? This action cannot be undone.')) {
        fetch(`/admin/banks/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Bank deleted successfully!');
                location.reload();
            } else {
                alert(data.message || 'Failed to delete bank');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('An error occurred');
        });
    }
}
</script>
@endpush
@endsection

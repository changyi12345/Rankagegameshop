@extends('layouts.admin')

@section('title', 'Pending Payments - Admin Panel')
@section('page-title', 'Pending Payments')

@section('content')
<div class="space-y-6">
    <!-- Pending Payments -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-light-text">Pending Payments</h2>
            <span class="badge badge-warning">{{ count($pending_payments ?? []) }} Pending</span>
        </div>
        
        <div class="space-y-4">
            @forelse($pending_payments ?? [] as $payment)
            <div class="bg-dark-base rounded-xl p-4">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        @if($payment->order_id)
                            <p class="text-light-text font-semibold mb-1">Order #{{ $payment->order->order_id ?? 'N/A' }}</p>
                            <p class="text-gray-400 text-sm">{{ $payment->order->game->name ?? 'Game' }} - {{ number_format($payment->amount) }} Ks</p>
                        @else
                            <p class="text-light-text font-semibold mb-1">
                                💰 Wallet Top-up 
                                @if($payment->method === 'kpay')
                                    <span class="badge badge-info text-xs ml-2">KPay</span>
                                @elseif($payment->method === 'manual')
                                    <span class="badge badge-info text-xs ml-2">Manual</span>
                                @endif
                            </p>
                            <p class="text-gray-400 text-sm">
                                User: {{ $payment->user->name ?? 'N/A' }} ({{ $payment->user->phone ?? 'N/A' }})
                            </p>
                            <p class="text-gray-400 text-sm">Amount: {{ number_format($payment->amount) }} Ks</p>
                            @if($payment->method === 'kpay' && $payment->gateway_response && isset($payment->gateway_response['kpay_account']))
                                <p class="text-gray-400 text-sm">KPay Account: <span class="font-mono">{{ $payment->gateway_response['kpay_account'] }}</span></p>
                            @endif
                        @endif
                        <p class="text-gray-500 text-xs mt-1">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($payment->screenshot)
                    <a href="{{ asset('storage/' . $payment->screenshot) }}" target="_blank" class="text-primary hover:underline text-sm ml-4">View Screenshot</a>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <button onclick="approvePayment({{ $payment->id }})" 
                            id="approve-btn-{{ $payment->id }}"
                            class="btn-primary flex-1 py-2 text-sm">
                        <span class="approve-text">Approve</span>
                        <span class="approve-loading hidden">Processing...</span>
                    </button>
                    <button onclick="rejectPayment({{ $payment->id }})" 
                            id="reject-btn-{{ $payment->id }}"
                            class="btn-outline flex-1 py-2 text-sm">
                        <span class="reject-text">Reject</span>
                        <span class="reject-loading hidden">Processing...</span>
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <span class="text-4xl block mb-2">✅</span>
                <p>No pending payments</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectionModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-dark-card rounded-2xl p-6 max-w-md w-full border border-dark-border">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-light-text">Reject Payment</h3>
            <button onclick="closeRejectionModal()" class="text-gray-400 hover:text-light-text">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="rejectionForm" onsubmit="submitRejection(event)">
            <input type="hidden" id="rejectPaymentId" name="payment_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        Rejection Reason <span class="text-red-400">*</span>
                    </label>
                    <textarea 
                        id="rejectionReason" 
                        name="reason" 
                        required
                        rows="4"
                        class="input-field"
                        placeholder="Please provide a reason for rejection..."
                        maxlength="500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Required. Maximum 500 characters.</p>
                </div>
            </div>
            <div class="flex space-x-3 mt-6">
                <button type="submit" class="btn-primary flex-1 py-3">Reject Payment</button>
                <button type="button" onclick="closeRejectionModal()" class="btn-outline flex-1 py-3">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentRejectPaymentId = null;

function approvePayment(paymentId) {
    if (!confirm('Approve this payment? The user wallet will be credited.')) {
        return;
    }
    
    const approveBtn = document.getElementById(`approve-btn-${paymentId}`);
    const approveText = approveBtn.querySelector('.approve-text');
    const approveLoading = approveBtn.querySelector('.approve-loading');
    
    approveBtn.disabled = true;
    approveText.classList.add('hidden');
    approveLoading.classList.remove('hidden');
    
    fetch(`/admin/payments/${paymentId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Payment approved successfully!', 'success');
            // Reload after short delay
            setTimeout(() => location.reload(), 1000);
        } else {
            approveBtn.disabled = false;
            approveText.classList.remove('hidden');
            approveLoading.classList.add('hidden');
            showNotification(data.message || 'Failed to approve payment', 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        approveBtn.disabled = false;
        approveText.classList.remove('hidden');
        approveLoading.classList.add('hidden');
        showNotification('An error occurred. Please try again.', 'error');
    });
}

function rejectPayment(paymentId) {
    currentRejectPaymentId = paymentId;
    document.getElementById('rejectPaymentId').value = paymentId;
    document.getElementById('rejectionReason').value = '';
    document.getElementById('rejectionModal').classList.remove('hidden');
}

function closeRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
    currentRejectPaymentId = null;
    document.getElementById('rejectionReason').value = '';
}

function submitRejection(event) {
    event.preventDefault();
    
    const paymentId = document.getElementById('rejectPaymentId').value;
    const reason = document.getElementById('rejectionReason').value.trim();
    
    if (!reason) {
        showNotification('Please provide a rejection reason.', 'error');
        return;
    }
    
    const rejectBtn = document.getElementById(`reject-btn-${paymentId}`);
    const rejectText = rejectBtn.querySelector('.reject-text');
    const rejectLoading = rejectBtn.querySelector('.reject-loading');
    
    rejectBtn.disabled = true;
    rejectText.classList.add('hidden');
    rejectLoading.classList.remove('hidden');
    
    fetch(`/admin/payments/${paymentId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeRejectionModal();
            showNotification('Payment rejected successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            rejectBtn.disabled = false;
            rejectText.classList.remove('hidden');
            rejectLoading.classList.add('hidden');
            showNotification(data.message || 'Failed to reject payment', 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        rejectBtn.disabled = false;
        rejectText.classList.remove('hidden');
        rejectLoading.classList.add('hidden');
        showNotification('An error occurred. Please try again.', 'error');
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
</script>
@endpush
@endsection

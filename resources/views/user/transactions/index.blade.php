@extends('layouts.user')

@section('title', 'Transaction History - RanKage Game Shop')
@section('page-title', 'Transaction History')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <h2 class="text-xl font-bold text-light-text mb-4">Filters</h2>
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Type</label>
                <select name="type" class="input-field">
                    <option value="">All Types</option>
                    <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                    <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-field">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-field">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-primary px-6 py-3">Filter</button>
                <a href="{{ route('transactions.index') }}" class="btn-outline px-6 py-3">Reset</a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card">
            <p class="text-gray-400 text-sm mb-1">Total Transactions</p>
            <p class="text-2xl font-bold text-light-text">{{ number_format($totalTransactions) }}</p>
        </div>
        <div class="card">
            <p class="text-gray-400 text-sm mb-1">Total Credits</p>
            <p class="text-2xl font-bold text-secondary">
                {{ number_format($totalCredits) }} Ks
            </p>
        </div>
        <div class="card">
            <p class="text-gray-400 text-sm mb-1">Total Debits</p>
            <p class="text-2xl font-bold text-red-400">
                {{ number_format($totalDebits) }} Ks
            </p>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-light-text">Transaction History</h2>
            <span class="badge badge-info text-sm">{{ $transactions->total() }} Total</span>
        </div>
        
        <div class="space-y-3">
            @forelse($transactions as $transaction)
            @php
                $payment = $transaction->payment ?? null;
                $isRejected = $payment && $payment->status === 'rejected';
            @endphp
            <div class="flex items-center justify-between p-4 bg-dark-base rounded-xl {{ $isRejected ? 'border-l-4 border-red-500' : '' }}">
                <div class="flex items-center space-x-3 flex-1">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $transaction->type === 'credit' ? 'bg-secondary/20' : 'bg-primary/20' }}">
                        <span class="text-2xl">{{ $transaction->type === 'credit' ? '➕' : '➖' }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <p class="font-semibold text-light-text">{{ $transaction->description ?? 'Transaction' }}</p>
                            @if($isRejected)
                                <span class="badge badge-danger text-xs">Rejected</span>
                            @elseif($payment && $payment->status === 'pending')
                                <span class="badge badge-warning text-xs">Pending</span>
                            @elseif($payment && $payment->status === 'approved')
                                <span class="badge badge-success text-xs">Approved</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                        @if($payment && !$payment->order_id)
                            @if($payment->method === 'kpay')
                                <span class="badge badge-info text-xs mt-1 inline-block">💳 KPay</span>
                            @elseif($payment->method === 'manual')
                                <span class="badge badge-info text-xs mt-1 inline-block">🏦 Manual Bank</span>
                            @elseif($payment->method === 'wavepay')
                                <span class="badge badge-info text-xs mt-1 inline-block">📱 WavePay</span>
                            @endif
                        @endif
                        @if($isRejected && $payment->rejection_reason)
                            <div class="mt-2 p-2 bg-red-500/10 border border-red-500/30 rounded-lg">
                                <p class="text-xs text-red-400 font-semibold mb-1">Rejection Reason:</p>
                                <p class="text-xs text-red-300">{{ $payment->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold {{ $transaction->type === 'credit' ? 'text-secondary' : 'text-red-400' }}">
                        {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount) }} Ks
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Balance: {{ number_format($transaction->balance_after) }} Ks</p>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <span class="text-5xl mb-3 block">💳</span>
                <p class="text-gray-400 text-lg">No transactions yet</p>
                <p class="text-gray-500 text-sm mt-2">Your transaction history will appear here</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

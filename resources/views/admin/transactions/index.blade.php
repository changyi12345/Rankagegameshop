@extends('layouts.admin')

@section('title', 'Transaction History - Admin Panel')
@section('page-title', 'Transaction History')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <h2 class="text-xl font-bold text-light-text mb-4">Filters</h2>
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">User</label>
                <select name="user_id" class="input-field">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->phone }})
                        </option>
                    @endforeach
                </select>
            </div>

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

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Description..." class="input-field">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-primary px-6 py-3">Filter</button>
                <a href="{{ route('admin.transactions.index') }}" class="btn-outline px-6 py-3">Reset</a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
        <div class="card">
            <p class="text-gray-400 text-sm mb-1">Net Amount</p>
            <p class="text-2xl font-bold {{ $netAmount >= 0 ? 'text-secondary' : 'text-red-400' }}">
                {{ number_format($netAmount) }} Ks
            </p>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-light-text">All Transactions</h2>
            <span class="badge badge-info">{{ $transactions->total() }} Total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-border">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Date</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">User</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Type</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Description</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Amount</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Balance After</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    @php
                        $payment = $transaction->payment ?? null;
                        $isRejected = $payment && $payment->status === 'rejected';
                    @endphp
                    <tr class="border-b border-dark-border hover:bg-dark-base transition-colors {{ $isRejected ? 'bg-red-500/5' : '' }}">
                        <td class="py-3 px-4 text-gray-400 text-sm">
                            {{ $transaction->created_at->format('M d, Y') }}<br>
                            <span class="text-xs">{{ $transaction->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div>
                                <p class="text-light-text font-semibold">{{ $transaction->user->name ?? 'N/A' }}</p>
                                <p class="text-gray-400 text-xs">{{ $transaction->user->phone ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="space-y-1">
                                <span class="badge {{ $transaction->type === 'credit' ? 'badge-success' : 'badge-danger' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                                @if($payment && !$payment->order_id)
                                    @if($payment->method === 'kpay')
                                        <span class="badge badge-info text-xs block mt-1">💳 KPay</span>
                                    @elseif($payment->method === 'manual')
                                        <span class="badge badge-info text-xs block mt-1">🏦 Manual Bank</span>
                                    @elseif($payment->method === 'wavepay')
                                        <span class="badge badge-info text-xs block mt-1">📱 WavePay</span>
                                    @else
                                        <span class="badge badge-info text-xs block mt-1">{{ ucfirst($payment->method) }}</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <p class="text-light-text text-sm">{{ $transaction->description ?? 'Transaction' }}</p>
                            @if($isRejected && $payment->rejection_reason)
                                <p class="text-red-400 text-xs mt-1">Reason: {{ $payment->rejection_reason }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-bold {{ $transaction->type === 'credit' ? 'text-secondary' : 'text-red-400' }}">
                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount) }} Ks
                            </p>
                        </td>
                        <td class="py-3 px-4 text-light-text text-sm">
                            {{ number_format($transaction->balance_after) }} Ks
                        </td>
                        <td class="py-3 px-4">
                            @if($payment)
                                @if($payment->status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($payment->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @endif
                            @else
                                <span class="badge badge-info">Completed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <span class="text-4xl block mb-2">💳</span>
                            <p>No transactions found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection

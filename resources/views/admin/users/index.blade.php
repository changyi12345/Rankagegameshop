@extends('layouts.admin')

@section('title', 'Users Management - Admin Panel')
@section('page-title', 'Users Management')

@section('content')
<div class="space-y-6">
    <!-- Search and Filters -->
    <div class="card">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by name, phone, email..." 
                       class="input-field"
                       id="searchInput">
            </div>
            <button type="submit" class="btn-primary px-6 py-3">Search</button>
            <a href="{{ route('admin.users.index') }}" class="btn-outline px-6 py-3">Reset</a>
        </form>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-border">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">User</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Phone</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Email</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Balance</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Orders</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                    <tr class="border-b border-dark-border hover:bg-dark-base transition-colors">
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                                    <span class="text-lg">👤</span>
                                </div>
                                <div>
                                    <p class="text-light-text font-semibold">{{ $user->name ?? 'Guest' }}</p>
                                    @if($user->telegram_username)
                                        <p class="text-gray-400 text-xs">@{{ $user->telegram_username }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-light-text">{{ $user->phone ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-gray-400">{{ $user->email ?? 'N/A' }}</td>
                        <td class="py-3 px-4">
                            <span class="text-secondary font-semibold">{{ number_format($user->balance ?? 0) }} Ks</span>
                        </td>
                        <td class="py-3 px-4 text-light-text">{{ number_format($user->orders_count ?? 0) }}</td>
                        <td class="py-3 px-4">
                            @if($user->is_blocked ?? false)
                                <span class="badge badge-danger">Blocked</span>
                            @else
                                <span class="badge badge-success">Active</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-2">
                                <button onclick="adjustBalance({{ $user->id }})" class="text-primary hover:text-primary-light text-sm">Balance</button>
                                <button onclick="viewOrders({{ $user->id }})" class="text-secondary hover:text-secondary-light text-sm">Orders</button>
                                <button onclick="toggleBlock({{ $user->id }}, {{ $user->is_blocked ? 'false' : 'true' }})" 
                                        class="text-red-400 hover:text-red-300 text-sm">
                                    {{ $user->is_blocked ? 'Unblock' : 'Block' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="mt-6 flex items-center justify-between">
            <p class="text-gray-400 text-sm">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
            </p>
            <div class="flex space-x-2">
                @if($users->onFirstPage())
                    <button class="btn-outline px-4 py-2 text-sm opacity-50 cursor-not-allowed" disabled>Previous</button>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="btn-outline px-4 py-2 text-sm">Previous</a>
                @endif
                
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="btn-outline px-4 py-2 text-sm">Next</a>
                @else
                    <button class="btn-outline px-4 py-2 text-sm opacity-50 cursor-not-allowed" disabled>Next</button>
                @endif
            </div>
        </div>
        @elseif($users->total() > 0)
        <div class="mt-6">
            <p class="text-gray-400 text-sm">
                Showing {{ $users->total() }} user(s)
            </p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function adjustBalance(userId) {
    const amount = prompt('Enter amount to adjust (positive to add, negative to deduct):');
    if (amount !== null && amount !== '') {
        fetch(`/admin/users/${userId}/balance`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ amount: parseFloat(amount) })
        }).then(() => location.reload());
    }
}

function viewOrders(userId) {
    window.location.href = `/admin/orders?user_id=${userId}`;
}

function toggleBlock(userId, block) {
    if (confirm(block ? 'Block this user?' : 'Unblock this user?')) {
        fetch(`/admin/users/${userId}/block`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ block: block === 'true' })
        }).then(() => location.reload());
    }
}
</script>
@endpush
@endsection

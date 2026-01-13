@extends('layouts.admin')

@section('title', 'Games Management - Admin Panel')
@section('page-title', 'Games Management')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-light-text">Games</h2>
        <a href="/admin/games/create" class="btn-primary px-6 py-3">Add New Game</a>
    </div>

    <!-- Games Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($games ?? [] as $game)
        <div class="card hover:bg-dark-base transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                        <span class="text-4xl">{{ $game->icon ?? '🎮' }}</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-light-text">{{ $game->name }}</h3>
                        <p class="text-gray-400 text-sm">{{ $game->currency_name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               {{ $game->is_active ? 'checked' : '' }}
                               onchange="toggleGame({{ $game->id }}, this.checked)"
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-400">Packages</span>
                    <span class="text-light-text font-semibold">{{ $game->packages_count ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-400">Total Orders</span>
                    <span class="text-light-text font-semibold">{{ number_format($game->orders_count ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-400">Profit Margin</span>
                    <span class="text-secondary font-semibold">{{ $game->profit_margin ?? 0 }}%</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <a href="/admin/games/{{ $game->id }}/edit" class="btn-outline flex-1 text-center py-2 text-sm">Edit</a>
                <a href="/admin/games/{{ $game->id }}/packages" class="btn-primary flex-1 text-center py-2 text-sm">Packages</a>
            </div>
        </div>
        @empty
        <div class="col-span-full card text-center py-12">
            <span class="text-5xl mb-4 block">🎮</span>
            <h3 class="text-xl font-bold text-light-text mb-2">No games found</h3>
            <p class="text-gray-400 mb-6">Add your first game to get started</p>
            <a href="/admin/games/create" class="btn-primary inline-block px-6 py-3">Add New Game</a>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function toggleGame(gameId, isActive) {
    fetch(`/admin/games/${gameId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ is_active: isActive })
    }).then(res => {
        if (!res.ok) {
            location.reload(); // Reload on error to revert toggle
        }
    });
}
</script>
@endpush
@endsection

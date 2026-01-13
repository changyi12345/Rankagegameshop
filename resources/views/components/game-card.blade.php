@props(['game', 'href' => null])

@php
$url = $href ?? "/games/{$game->id ?? ''}";
@endphp

<a href="{{ $url }}" class="card hover:scale-105 transition-transform group cursor-pointer block">
    <div class="aspect-square rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center mb-3 relative overflow-hidden">
        <span class="text-6xl">{{ $game->icon ?? '🎮' }}</span>
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
    </div>
    <h3 class="font-bold text-light-text mb-1">{{ $game->name ?? 'Game' }}</h3>
    <p class="text-xs text-gray-400 mb-2">{{ $game->currency_name ?? 'Currency' }}</p>
    <div class="flex items-center">
        <span class="text-xs text-secondary font-semibold">Starting at</span>
        <span class="text-sm font-bold text-light-text ml-2">{{ number_format($game->min_price ?? 1000) }} Ks</span>
    </div>
    @if(!($game->is_active ?? true))
    <div class="mt-2">
        <span class="badge badge-danger">Unavailable</span>
    </div>
    @endif
</a>

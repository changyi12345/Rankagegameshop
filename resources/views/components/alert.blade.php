@props(['type' => 'info', 'message' => '', 'dismissible' => false])

@php
$typeClasses = [
    'success' => 'bg-secondary/10 border-secondary/30 text-secondary',
    'error' => 'bg-red-500/10 border-red-500/30 text-red-400',
    'warning' => 'bg-yellow-500/10 border-yellow-500/30 text-yellow-400',
    'info' => 'bg-primary/10 border-primary/30 text-primary',
];
$classes = $typeClasses[$type] ?? $typeClasses['info'];
@endphp

<div class="rounded-xl p-4 border {{ $classes }} flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex items-center space-x-3">
        @if($type === 'success')
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        @elseif($type === 'error')
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        @elseif($type === 'warning')
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        @else
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        @endif
        <span class="text-sm">{{ $message ?: $slot }}</span>
    </div>
    @if($dismissible)
    <button @click="show = false" class="ml-4 text-current hover:opacity-75">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
    @endif
</div>

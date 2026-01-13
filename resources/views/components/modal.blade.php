@props(['id' => 'modal', 'title' => '', 'size' => 'md'])

@php
$sizeClasses = [
    'sm' => 'max-w-md',
    'md' => 'max-w-lg',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
];
@endphp

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" x-data="{ open: false }" x-show="open" @keydown.escape.window="open = false">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="open = false"></div>
        
        <!-- Modal -->
        <div class="relative bg-dark-card rounded-2xl {{ $sizeClasses[$size] }} w-full border border-dark-border shadow-2xl transform transition-all">
            <!-- Header -->
            @if($title)
            <div class="flex items-center justify-between p-6 border-b border-dark-border">
                <h3 class="text-xl font-bold text-light-text">{{ $title }}</h3>
                <button @click="open = false" class="text-gray-400 hover:text-light-text">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            @endif
            
            <!-- Content -->
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

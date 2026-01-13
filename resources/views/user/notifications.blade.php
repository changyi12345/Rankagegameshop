@extends('layouts.user')

@section('title', 'Notifications - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-4xl">
    <h1 class="text-2xl font-bold text-light-text mb-6 flex items-center">
        <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
        Notifications
    </h1>

    <div class="space-y-4">
        @forelse($notifications ?? [] as $notification)
        <div class="card {{ !$notification->is_read ? 'bg-primary/5 border-l-4 border-primary' : '' }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <h3 class="font-bold text-light-text">{{ $notification->title }}</h3>
                        @if(!$notification->is_read)
                            <span class="w-2 h-2 bg-primary rounded-full"></span>
                        @endif
                    </div>
                    <p class="text-gray-400 text-sm mb-2">{{ $notification->message }}</p>
                    <p class="text-gray-500 text-xs">{{ $notification->created_at->format('M d, Y h:i A') }}</p>
                </div>
                @if(!$notification->is_read)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="ml-4">
                    @csrf
                    <button type="submit" class="text-primary hover:text-primary-light text-xs">
                        Mark as read
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="card text-center py-12">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <p class="text-gray-400">No notifications yet</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($notifications) && $notifications->hasPages())
    <div class="mt-6 flex items-center justify-center space-x-2">
        @if($notifications->onFirstPage())
            <button class="btn-outline px-4 py-2 text-sm opacity-50 cursor-not-allowed" disabled>Previous</button>
        @else
            <a href="{{ $notifications->previousPageUrl() }}" class="btn-outline px-4 py-2 text-sm">Previous</a>
        @endif
        
        @if($notifications->hasMorePages())
            <a href="{{ $notifications->nextPageUrl() }}" class="btn-outline px-4 py-2 text-sm">Next</a>
        @else
            <button class="btn-outline px-4 py-2 text-sm opacity-50 cursor-not-allowed" disabled>Next</button>
        @endif
    </div>
    @endif
</div>
@endsection

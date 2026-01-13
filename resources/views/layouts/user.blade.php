<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RanKage Game Shop')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Myanmar:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if(file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp
        @if(isset($manifest['resources/css/app.css']))
            <link rel="stylesheet" href="/build/{{ $manifest['resources/css/app.css']['file'] }}">
        @endif
        @if(isset($manifest['resources/js/app.js']))
            <script type="module" src="/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-dark-bg text-light-text font-sans antialiased">
    <!-- Mobile-First User Layout -->
    <div class="min-h-screen pb-20">
        <!-- Header (Mobile) -->
        <header class="bg-dark-card border-b border-dark-border sticky top-0 z-50">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center overflow-hidden flex-shrink-0">
                            @php
                                $siteLogo = \App\Models\Setting::get('site_logo');
                                $siteName = \App\Models\Setting::get('site_name', 'RanKage');
                                $siteTagline = \App\Models\Setting::get('site_tagline', 'Game Shop');
                            @endphp
                            @if($siteLogo)
                                <img src="{{ asset('storage/' . $siteLogo) }}" 
                                     alt="{{ $siteName }} Logo" 
                                     class="w-full h-full object-contain p-1">
                            @else
                                <span class="text-2xl">🎮</span>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-light-text">{{ $siteName }}</h1>
                            <p class="text-xs text-gray-400">{{ $siteTagline }}</p>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-3">
                        @auth
                            <!-- Wallet Balance -->
                            <div class="hidden sm:flex items-center space-x-2 bg-dark-base px-3 py-1.5 rounded-lg">
                                <span class="text-xs text-gray-400">Balance</span>
                                <span class="text-sm font-bold text-secondary">{{ number_format(auth()->user()->balance ?? 0) }} Ks</span>
                            </div>
                            
                            <!-- Notifications Bell -->
                            <div class="relative" x-data="notificationData()" x-init="init()">
                                <button @click="toggleNotifications" class="w-10 h-10 rounded-full bg-dark-base flex items-center justify-center relative hover:bg-dark-card transition-colors">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <span x-show="unreadCount > 0" 
                                          x-text="unreadCount > 99 ? '99+' : unreadCount"
                                          class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold"></span>
                                </button>
                                
                                <!-- Notifications Dropdown -->
                                <div x-show="showNotifications" 
                                     x-transition
                                     @click.away="showNotifications = false"
                                     class="absolute right-0 top-12 w-80 bg-dark-card border border-dark-border rounded-xl shadow-2xl z-50 max-h-96 overflow-hidden">
                                    <div class="p-4 border-b border-dark-border flex items-center justify-between">
                                        <h3 class="font-bold text-light-text">Notifications</h3>
                                        <button @click="markAllAsRead" class="text-xs text-primary hover:underline">Mark all as read</button>
                                    </div>
                                    <div class="overflow-y-auto max-h-80">
                                        <template x-if="notifications.length === 0">
                                            <div class="p-8 text-center text-gray-400">
                                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                                </svg>
                                                <p>No notifications</p>
                                            </div>
                                        </template>
                                        <template x-for="notification in notifications" :key="notification.id">
                                            <div @click="markAsRead(notification.id)" 
                                                 :class="notification.is_read ? 'bg-dark-base' : 'bg-primary/5 border-l-2 border-primary'"
                                                 class="p-4 border-b border-dark-border cursor-pointer hover:bg-dark-base transition-colors">
                                                <p class="font-semibold text-light-text text-sm mb-1" x-text="notification.title"></p>
                                                <p class="text-gray-400 text-xs" x-text="notification.message"></p>
                                                <p class="text-gray-500 text-xs mt-2" x-text="formatDate(notification.created_at)"></p>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="p-3 border-t border-dark-border text-center">
                                        <a href="/notifications" class="text-primary text-sm hover:underline">View All</a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Icon -->
                            <a href="/profile" class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </a>
                        @else
                            <a href="/login" class="btn-primary px-4 py-2 text-sm">
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Bottom Navigation (Mobile) -->
        <nav class="fixed bottom-0 left-0 right-0 bg-dark-card border-t border-dark-border z-50 sm:hidden">
            <div class="flex items-center justify-around py-2">
                <a href="/" class="flex flex-col items-center space-y-1 px-4 py-2 {{ request()->is('/') ? 'text-primary' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-xs">Home</span>
                </a>
                <a href="/orders" class="flex flex-col items-center space-y-1 px-4 py-2 {{ request()->is('orders*') ? 'text-primary' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span class="text-xs">Orders</span>
                </a>
                <a href="/wallet" class="flex flex-col items-center space-y-1 px-4 py-2 {{ request()->is('wallet*') && !request()->is('transactions*') ? 'text-primary' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-xs">Wallet</span>
                </a>
                @auth
                <a href="/transactions" class="flex flex-col items-center space-y-1 px-4 py-2 {{ request()->is('transactions*') ? 'text-primary' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="text-xs">Transactions</span>
                </a>
                <a href="/profile" class="flex flex-col items-center space-y-1 px-4 py-2 {{ request()->is('profile*') ? 'text-primary' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-xs">Profile</span>
                </a>
                @else
                <a href="/login" class="flex flex-col items-center space-y-1 px-4 py-2 {{ request()->is('login*') ? 'text-primary' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-xs">Login</span>
                </a>
                @endauth
            </div>
        </nav>
    </div>

    @stack('scripts')
    
    @auth
    <script>
    function notificationData() {
        return {
            showNotifications: false,
            notifications: [],
            unreadCount: 0,
            
            init() {
                this.fetchNotifications();
                // Poll for new notifications every 30 seconds
                setInterval(() => this.fetchNotifications(), 30000);
            },
            
            async fetchNotifications() {
                try {
                    const response = await fetch('/notifications/api', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await response.json();
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.unread_count || 0;
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                }
            },
            
            toggleNotifications() {
                this.showNotifications = !this.showNotifications;
                if (this.showNotifications) {
                    this.fetchNotifications();
                }
            },
            
            async markAsRead(notificationId) {
                try {
                    await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    // Update local state
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification) {
                        notification.is_read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            },
            
            async markAllAsRead() {
                try {
                    await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    // Update local state
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            },
            
            formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                const seconds = Math.floor(diff / 1000);
                const minutes = Math.floor(seconds / 60);
                const hours = Math.floor(minutes / 60);
                const days = Math.floor(hours / 24);
                
                if (days > 7) {
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                } else if (days > 0) {
                    return `${days} day${days > 1 ? 's' : ''} ago`;
                } else if (hours > 0) {
                    return `${hours} hour${hours > 1 ? 's' : ''} ago`;
                } else if (minutes > 0) {
                    return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
                } else {
                    return 'Just now';
                }
            }
        }
    }
    </script>
    @endauth
</body>
</html>

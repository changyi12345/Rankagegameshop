<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin Panel - RanKage Game Shop'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Myanmar:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php if(file_exists(public_path('build/manifest.json'))): ?>
        <?php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        ?>
        <?php if(isset($manifest['resources/css/app.css'])): ?>
            <link rel="stylesheet" href="/build/<?php echo e($manifest['resources/css/app.css']['file']); ?>">
        <?php endif; ?>
        <?php if(isset($manifest['resources/js/app.js'])): ?>
            <script type="module" src="/build/<?php echo e($manifest['resources/js/app.js']['file']); ?>"></script>
        <?php endif; ?>
    <?php else: ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php endif; ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-dark-bg text-light-text font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar (Desktop) -->
        <aside class="hidden lg:flex lg:flex-shrink-0">
            <div class="w-64 bg-dark-card border-r border-dark-border flex flex-col h-screen">
                <!-- Logo -->
                <div class="p-6 border-b border-dark-border flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center overflow-hidden flex-shrink-0">
                            <?php if(auth()->user()->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" 
                                     alt="<?php echo e(auth()->user()->name); ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-2xl">🎮</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-light-text">RanKage</h1>
                            <p class="text-xs text-gray-400">Admin Panel</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto overflow-x-hidden" style="max-height: calc(100vh - 200px);">
                    <a href="/admin/dashboard" class="nav-item <?php echo e(request()->is('admin/dashboard*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="/admin/promotions" class="nav-item <?php echo e(request()->is('admin/promotions*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <span>Promotions</span>
                    </a>
                    <a href="/admin/logo" class="nav-item <?php echo e(request()->is('admin/logo*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Logo</span>
                    </a>
                    <a href="/admin/games" class="nav-item <?php echo e(request()->is('admin/games*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Games</span>
                    </a>
                    <a href="/admin/orders" class="nav-item <?php echo e(request()->is('admin/orders*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Orders</span>
                    </a>
                    <a href="/admin/users" class="nav-item <?php echo e(request()->is('admin/users*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Users</span>
                    </a>
                    <a href="/admin/payments" class="nav-item <?php echo e(request()->is('admin/payments*') && !request()->is('admin/banks*') && !request()->is('admin/transactions*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Payments</span>
                    </a>
                    <a href="/admin/transactions" class="nav-item <?php echo e(request()->is('admin/transactions*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span>Transactions</span>
                    </a>
                    <a href="/admin/banks" class="nav-item <?php echo e(request()->is('admin/banks*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <span>Banks</span>
                    </a>
                    <a href="/admin/api-settings" class="nav-item <?php echo e(request()->is('admin/api-settings*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>API Settings</span>
                    </a>
                    <a href="/admin/notifications" class="nav-item <?php echo e(request()->is('admin/notifications*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span>Notifications</span>
                    </a>
                </nav>

                <!-- User Section -->
                <div class="p-4 border-t border-dark-border flex-shrink-0">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <?php if(auth()->user()->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" 
                                     alt="<?php echo e(auth()->user()->name); ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-lg">👤</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-light-text text-sm truncate"><?php echo e(auth()->user()->name ?? 'Admin'); ?></p>
                            <p class="text-xs text-gray-400 truncate"><?php echo e(auth()->user()->email ?? 'admin@rankage.com'); ?></p>
                        </div>
                    </div>
                    <a href="/admin/profile" class="w-full btn-outline text-sm py-2 text-center block mb-2">Profile</a>
                    <a href="/logout" class="w-full btn-outline text-sm py-2 text-center block">Logout</a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-dark-card border-b border-dark-border px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button class="lg:hidden text-gray-400 hover:text-light-text" onclick="toggleMobileMenu()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="text-xl font-bold text-light-text"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-400 hover:text-light-text">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar -->
    <div id="mobileSidebar" class="fixed inset-0 z-50 lg:hidden hidden">
        <div class="absolute inset-0 bg-black/50" onclick="toggleMobileMenu()"></div>
        <div class="absolute left-0 top-0 bottom-0 w-64 bg-dark-card border-r border-dark-border">
            <!-- Same sidebar content as desktop -->
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script>
        function toggleMobileMenu() {
            document.getElementById('mobileSidebar').classList.toggle('hidden');
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\RanKeagegmshop\resources\views/layouts/admin.blade.php ENDPATH**/ ?>
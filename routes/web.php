<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\{
    AuthController as UserAuthController,
    HomeController,
    GameController,
    OrderController,
    PaymentController,
    ProfileController,
    WalletController,
    SupportController
};
use App\Http\Controllers\Admin\{
    AuthController as AdminAuthController,
    DashboardController,
    GameController as AdminGameController,
    OrderController as AdminOrderController,
    UserController as AdminUserController,
    PaymentController as AdminPaymentController,
    TransactionController,
    BankController,
    ApiSettingsController,
    NotificationController
};

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication
Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/auth/send-otp', [UserAuthController::class, 'sendOTP']);
Route::post('/auth/verify-otp', [UserAuthController::class, 'verifyOTP']);
Route::post('/auth/register', [UserAuthController::class, 'register'])->name('auth.register');
Route::post('/auth/login-email', [UserAuthController::class, 'loginWithEmail'])->name('auth.login-email');
Route::post('/auth/telegram', [UserAuthController::class, 'telegramLogin'])->name('auth.telegram');
Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout')->middleware('auth');

// Games
Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');

// Orders
Route::middleware('auth')->group(function () {
    // Check player ID (for Mobile Legends)
    Route::post('/games/check-player-id', [GameController::class, 'checkPlayerId'])->name('games.check-player-id');
    
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/create', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/update-account', [OrderController::class, 'updateAccountInfo'])->name('orders.update-account');
    
    // Payment
    Route::get('/payment/{orderId}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    
    // Wallet
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
    Route::post('/wallet/top-up', [WalletController::class, 'topUp'])->name('wallet.topup');
    
    // Transactions
    Route::get('/transactions', [\App\Http\Controllers\User\TransactionController::class, 'index'])->name('transactions.index');
    
    // Products (Auto TopUp)
    Route::get('/products', [\App\Http\Controllers\User\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [\App\Http\Controllers\User\ProductController::class, 'show'])->name('products.show');
    Route::post('/products/{id}/purchase', [\App\Http\Controllers\User\ProductController::class, 'purchase'])->name('products.purchase');
    
    // Support
    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::post('/support/contact', [SupportController::class, 'contact'])->name('support.contact');
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\User\NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/api', [\App\Http\Controllers\User\NotificationController::class, 'api'])->name('notifications.api');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\User\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\User\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Authentication
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login')->middleware('guest:web');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Protected Admin Routes
    Route::middleware(['auth', 'admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Profile
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('profile.password');
        
        // Logo
        Route::get('/logo', [\App\Http\Controllers\Admin\LogoController::class, 'index'])->name('logo.index');
        Route::post('/logo', [\App\Http\Controllers\Admin\LogoController::class, 'update'])->name('logo.update');
        
        // Promotions
        Route::get('/promotions', [\App\Http\Controllers\Admin\PromotionController::class, 'index'])->name('promotions.index');
        Route::post('/promotions', [\App\Http\Controllers\Admin\PromotionController::class, 'update'])->name('promotions.update');
        
        // Games
        Route::get('/games', [AdminGameController::class, 'index'])->name('games.index');
        Route::get('/games/create', [AdminGameController::class, 'create'])->name('games.create');
        Route::post('/games', [AdminGameController::class, 'store'])->name('games.store');
        Route::get('/games/{id}/edit', [AdminGameController::class, 'edit'])->name('games.edit');
        Route::put('/games/{id}', [AdminGameController::class, 'update'])->name('games.update');
        Route::post('/games/{id}/toggle', [AdminGameController::class, 'toggle'])->name('games.toggle');
        // More specific routes first to avoid route matching conflicts
        Route::get('/games/{id}/packages/fetch-g2bulk', [AdminGameController::class, 'fetchG2BulkPackages'])->name('games.packages.fetch-g2bulk');
        Route::post('/games/{id}/packages/import-g2bulk', [AdminGameController::class, 'importG2BulkPackage'])->name('games.packages.import-g2bulk');
        Route::get('/games/{id}/packages', [AdminGameController::class, 'packages'])->name('games.packages');
        Route::post('/games/{id}/packages', [AdminGameController::class, 'storePackage'])->name('games.packages.store');
        Route::get('/games/{gameId}/packages/{packageId}', [AdminGameController::class, 'getPackage'])->name('games.packages.get');
        Route::put('/games/{gameId}/packages/{packageId}', [AdminGameController::class, 'updatePackage'])->name('games.packages.update');
        Route::delete('/games/{gameId}/packages/{packageId}', [AdminGameController::class, 'deletePackage'])->name('games.packages.delete');
        
        // Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/retry', [AdminOrderController::class, 'retry'])->name('orders.retry');
        Route::post('/orders/{id}/manual-complete', [AdminOrderController::class, 'manualComplete'])->name('orders.manual-complete');
        Route::post('/orders/{id}/refund', [AdminOrderController::class, 'refund'])->name('orders.refund');
        
        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{id}/balance', [AdminUserController::class, 'adjustBalance'])->name('users.balance');
        Route::post('/users/{id}/block', [AdminUserController::class, 'toggleBlock'])->name('users.block');
        
        // Payments
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/toggle-method', [AdminPaymentController::class, 'toggleMethod'])->name('payments.toggle-method');
        Route::post('/payments/{id}/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');
        Route::post('/payments/bank-details', [AdminPaymentController::class, 'updateBankDetails'])->name('payments.bank-details');
        
        // Banks
        Route::get('/banks', [BankController::class, 'index'])->name('banks.index');
        Route::get('/banks/{id}', [BankController::class, 'show'])->name('banks.show');
        Route::post('/banks', [BankController::class, 'store'])->name('banks.store');
        Route::put('/banks/{id}', [BankController::class, 'update'])->name('banks.update');
        Route::delete('/banks/{id}', [BankController::class, 'destroy'])->name('banks.destroy');
        Route::post('/banks/{id}/toggle', [BankController::class, 'toggle'])->name('banks.toggle');
        
        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        
        // API Settings
        Route::get('/api-settings', [ApiSettingsController::class, 'index'])->name('api-settings.index');
        Route::post('/api-settings/save', [ApiSettingsController::class, 'save'])->name('api-settings.save');
        Route::post('/api-settings/check-balance', [ApiSettingsController::class, 'checkBalance'])->name('api-settings.check-balance');
        Route::post('/api-settings/test-connection', [ApiSettingsController::class, 'testConnection'])->name('api-settings.test-connection');
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/save-bot', [NotificationController::class, 'saveBot'])->name('notifications.save-bot');
        Route::post('/notifications/check-bot', [NotificationController::class, 'checkBotStatus'])->name('notifications.check-bot');
        Route::post('/notifications/verify-chat', [NotificationController::class, 'verifyChatId'])->name('notifications.verify-chat');
        Route::post('/notifications/test', [NotificationController::class, 'sendTest'])->name('notifications.test');
        Route::post('/notifications/broadcast', [NotificationController::class, 'broadcast'])->name('notifications.broadcast');
    });
});

/*
|--------------------------------------------------------------------------
| Webhook Routes (Public, no auth required)
|--------------------------------------------------------------------------
*/

// G2Bulk webhook callback
Route::post('/webhook/g2bulk', [\App\Http\Controllers\WebhookController::class, 'g2bulkCallback'])->name('webhook.g2bulk');
// Test endpoint for webhook (GET request for browser testing)
Route::get('/webhook/g2bulk/test', [\App\Http\Controllers\WebhookController::class, 'testWebhook'])->name('webhook.g2bulk.test');


<?php $__env->startSection('title', 'Order #' . ($order->order_id ?? '') . ' - Admin Panel'); ?>
<?php $__env->startSection('page-title', 'Order Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl space-y-6">
    <!-- Back Button -->
    <a href="/admin/orders" class="inline-flex items-center text-gray-400 hover:text-primary mb-4 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Orders
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Order Information</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Order ID</span>
                        <span class="text-light-text font-bold">#<?php echo e($order->order_id ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Status</span>
                        <?php if($order->status === 'completed'): ?>
                            <span class="badge badge-success">Success</span>
                        <?php elseif($order->status === 'pending'): ?>
                            <span class="badge badge-warning">Pending</span>
                        <?php elseif($order->status === 'processing'): ?>
                            <span class="badge badge-info">Processing</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Failed</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Amount</span>
                        <span class="text-secondary text-xl font-bold"><?php echo e(number_format($order->amount ?? 0)); ?> Ks</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Created At</span>
                        <span class="text-light-text"><?php echo e($order->created_at->format('M d, Y h:i A') ?? 'N/A'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Game Details -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Game Details</h2>
                <div class="flex items-center space-x-4 p-4 bg-dark-base rounded-xl">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                        <span class="text-4xl"><?php echo e($order->game->icon ?? '🎮'); ?></span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-light-text"><?php echo e($order->game->name ?? 'Unknown Game'); ?></h3>
                        <p class="text-gray-400"><?php echo e($order->package->name ?? 'Package'); ?></p>
                        <div class="mt-2 space-y-1">
                            <?php if($order->user_game_id): ?>
                            <p class="text-sm">
                                <span class="text-gray-400">
                                    <?php if(stripos($order->game->name ?? '', 'Mobile Legends') !== false || stripos($order->game->name ?? '', 'MLBB') !== false): ?>
                                        Game ID:
                                    <?php else: ?>
                                        User ID:
                                    <?php endif; ?>
                                </span> 
                                <span class="text-light-text font-semibold"><?php echo e($order->user_game_id); ?></span>
                            </p>
                            <?php endif; ?>
                            <?php if($order->server_id): ?>
                            <p class="text-sm">
                                <span class="text-gray-400">
                                    <?php if(stripos($order->game->name ?? '', 'Mobile Legends') !== false || stripos($order->game->name ?? '', 'MLBB') !== false): ?>
                                        Zone ID:
                                    <?php else: ?>
                                        Server ID:
                                    <?php endif; ?>
                                </span> 
                                <span class="text-light-text font-semibold"><?php echo e($order->server_id); ?></span>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Payment Details</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Payment Method</span>
                        <span class="text-light-text font-semibold capitalize"><?php echo e($order->payment_method ?? 'N/A'); ?></span>
                    </div>
                    <?php if($order->payment): ?>
                    <div class="flex items-center justify-between p-3 bg-dark-base rounded-xl">
                        <span class="text-gray-400">Payment Status</span>
                        <?php if($order->payment->status === 'approved'): ?>
                            <span class="badge badge-success">Approved</span>
                        <?php elseif($order->payment->status === 'pending'): ?>
                            <span class="badge badge-warning">Pending</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Rejected</span>
                        <?php endif; ?>
                    </div>
                    <?php if($order->payment->screenshot): ?>
                    <div class="p-3 bg-dark-base rounded-xl">
                        <p class="text-gray-400 text-sm mb-2">Payment Screenshot</p>
                        <a href="<?php echo e(asset('storage/' . $order->payment->screenshot)); ?>" target="_blank" class="text-primary hover:underline">View Screenshot</a>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- G2Bulk API Response -->
            <?php if($order->api_response): ?>
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">API Response</h2>
                <div class="bg-dark-base rounded-xl p-4">
                    <pre class="text-xs text-gray-400 overflow-x-auto"><?php echo e(json_encode($order->api_response, JSON_PRETTY_PRINT)); ?></pre>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">Actions</h2>
                <div class="space-y-3">
                    <?php if($order->status === 'pending'): ?>
                    <button onclick="retryOrder()" class="btn-primary w-full py-3">Retry Top-up</button>
                    <button onclick="manualComplete()" class="btn-outline w-full py-3">Manual Complete</button>
                    <?php endif; ?>
                    <?php if($order->status === 'failed'): ?>
                    <button onclick="refundOrder()" class="btn-secondary w-full py-3">Refund to Wallet</button>
                    <?php endif; ?>
                    <?php if($order->payment && $order->payment->status === 'pending'): ?>
                    <button onclick="approvePayment()" class="btn-primary w-full py-3">Approve Payment</button>
                    <button onclick="rejectPayment()" class="btn-outline w-full py-3">Reject Payment</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Info -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-4">User Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Name</p>
                        <p class="text-light-text font-semibold"><?php echo e($order->user->name ?? 'Guest'); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Phone</p>
                        <p class="text-light-text"><?php echo e($order->user->phone ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Email</p>
                        <p class="text-light-text"><?php echo e($order->user->email ?? 'N/A'); ?></p>
                    </div>
                    <a href="/admin/users/<?php echo e($order->user->id ?? ''); ?>" class="btn-outline w-full py-2 text-sm text-center mt-4">View User Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function retryOrder() {
    if (confirm('Retry this order?')) {
        fetch('/admin/orders/<?php echo e($order->id ?? 0); ?>/retry', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

function manualComplete() {
    if (confirm('Manually mark this order as completed?')) {
        fetch('/admin/orders/<?php echo e($order->id ?? 0); ?>/manual-complete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

function refundOrder() {
    if (confirm('Refund this order to user wallet?')) {
        fetch('/admin/orders/<?php echo e($order->id ?? 0); ?>/refund', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

function approvePayment() {
    if (confirm('Approve this payment?')) {
        fetch('/admin/payments/<?php echo e($order->payment->id ?? 0); ?>/approve', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}

function rejectPayment() {
    const reason = prompt('Rejection reason:');
    if (reason !== null) {
        fetch('/admin/payments/<?php echo e($order->payment->id ?? 0); ?>/reject', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason })
        }).then(() => location.reload());
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'Transaction History - RanKage Game Shop'); ?>
<?php $__env->startSection('page-title', 'Transaction History'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <h2 class="text-xl font-bold text-light-text mb-4">Filters</h2>
        <form method="GET" action="<?php echo e(route('transactions.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Type</label>
                <select name="type" class="input-field">
                    <option value="">All Types</option>
                    <option value="credit" <?php echo e(request('type') == 'credit' ? 'selected' : ''); ?>>Credit</option>
                    <option value="debit" <?php echo e(request('type') == 'debit' ? 'selected' : ''); ?>>Debit</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Date From</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="input-field">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Date To</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="input-field">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-primary px-6 py-3">Filter</button>
                <a href="<?php echo e(route('transactions.index')); ?>" class="btn-outline px-6 py-3">Reset</a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card">
            <p class="text-gray-400 text-sm mb-1">Total Transactions</p>
            <p class="text-2xl font-bold text-light-text"><?php echo e(number_format($totalTransactions)); ?></p>
        </div>
        <div class="card">
            <p class="text-gray-400 text-sm mb-1">Total Credits</p>
            <p class="text-2xl font-bold text-secondary">
                <?php echo e(number_format($totalCredits)); ?> Ks
            </p>
        </div>
        <div class="card">
            <p class="text-gray-400 text-sm mb-1">Total Debits</p>
            <p class="text-2xl font-bold text-red-400">
                <?php echo e(number_format($totalDebits)); ?> Ks
            </p>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-light-text">Transaction History</h2>
            <span class="badge badge-info text-sm"><?php echo e($transactions->total()); ?> Total</span>
        </div>
        
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $payment = $transaction->payment ?? null;
                $isRejected = $payment && $payment->status === 'rejected';
            ?>
            <div class="flex items-center justify-between p-4 bg-dark-base rounded-xl <?php echo e($isRejected ? 'border-l-4 border-red-500' : ''); ?>">
                <div class="flex items-center space-x-3 flex-1">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center <?php echo e($transaction->type === 'credit' ? 'bg-secondary/20' : 'bg-primary/20'); ?>">
                        <span class="text-2xl"><?php echo e($transaction->type === 'credit' ? '➕' : '➖'); ?></span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <p class="font-semibold text-light-text"><?php echo e($transaction->description ?? 'Transaction'); ?></p>
                            <?php if($isRejected): ?>
                                <span class="badge badge-danger text-xs">Rejected</span>
                            <?php elseif($payment && $payment->status === 'pending'): ?>
                                <span class="badge badge-warning text-xs">Pending</span>
                            <?php elseif($payment && $payment->status === 'approved'): ?>
                                <span class="badge badge-success text-xs">Approved</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-400"><?php echo e($transaction->created_at->format('M d, Y h:i A')); ?></p>
                        <?php if($payment && !$payment->order_id): ?>
                            <?php if($payment->method === 'kpay'): ?>
                                <span class="badge badge-info text-xs mt-1 inline-block">💳 KPay</span>
                            <?php elseif($payment->method === 'manual'): ?>
                                <span class="badge badge-info text-xs mt-1 inline-block">🏦 Manual Bank</span>
                            <?php elseif($payment->method === 'wavepay'): ?>
                                <span class="badge badge-info text-xs mt-1 inline-block">📱 WavePay</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if($isRejected && $payment->rejection_reason): ?>
                            <div class="mt-2 p-2 bg-red-500/10 border border-red-500/30 rounded-lg">
                                <p class="text-xs text-red-400 font-semibold mb-1">Rejection Reason:</p>
                                <p class="text-xs text-red-300"><?php echo e($payment->rejection_reason); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold <?php echo e($transaction->type === 'credit' ? 'text-secondary' : 'text-red-400'); ?>">
                        <?php echo e($transaction->type === 'credit' ? '+' : '-'); ?><?php echo e(number_format($transaction->amount)); ?> Ks
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Balance: <?php echo e(number_format($transaction->balance_after)); ?> Ks</p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-12">
                <span class="text-5xl mb-3 block">💳</span>
                <p class="text-gray-400 text-lg">No transactions yet</p>
                <p class="text-gray-500 text-sm mt-2">Your transaction history will appear here</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if($transactions->hasPages()): ?>
        <div class="mt-6">
            <?php echo e($transactions->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/user/transactions/index.blade.php ENDPATH**/ ?>
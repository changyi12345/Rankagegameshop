

<?php $__env->startSection('title', 'Transaction History - Admin Panel'); ?>
<?php $__env->startSection('page-title', 'Transaction History'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <h2 class="text-xl font-bold text-light-text mb-4">Filters</h2>
        <form method="GET" action="<?php echo e(route('admin.transactions.index')); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">User</label>
                <select name="user_id" class="input-field">
                    <option value="">All Users</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                            <?php echo e($user->name); ?> (<?php echo e($user->phone); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

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

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Search</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Description..." class="input-field">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-primary px-6 py-3">Filter</button>
                <a href="<?php echo e(route('admin.transactions.index')); ?>" class="btn-outline px-6 py-3">Reset</a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
        <div class="card">
            <p class="text-gray-400 text-sm mb-1">Net Amount</p>
            <p class="text-2xl font-bold <?php echo e($netAmount >= 0 ? 'text-secondary' : 'text-red-400'); ?>">
                <?php echo e(number_format($netAmount)); ?> Ks
            </p>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-light-text">All Transactions</h2>
            <span class="badge badge-info"><?php echo e($transactions->total()); ?> Total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-border">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Date</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">User</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Type</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Description</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Amount</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Balance After</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $payment = $transaction->payment ?? null;
                        $isRejected = $payment && $payment->status === 'rejected';
                    ?>
                    <tr class="border-b border-dark-border hover:bg-dark-base transition-colors <?php echo e($isRejected ? 'bg-red-500/5' : ''); ?>">
                        <td class="py-3 px-4 text-gray-400 text-sm">
                            <?php echo e($transaction->created_at->format('M d, Y')); ?><br>
                            <span class="text-xs"><?php echo e($transaction->created_at->format('h:i A')); ?></span>
                        </td>
                        <td class="py-3 px-4">
                            <div>
                                <p class="text-light-text font-semibold"><?php echo e($transaction->user->name ?? 'N/A'); ?></p>
                                <p class="text-gray-400 text-xs"><?php echo e($transaction->user->phone ?? 'N/A'); ?></p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="space-y-1">
                                <span class="badge <?php echo e($transaction->type === 'credit' ? 'badge-success' : 'badge-danger'); ?>">
                                    <?php echo e(ucfirst($transaction->type)); ?>

                                </span>
                                <?php if($payment && !$payment->order_id): ?>
                                    <?php if($payment->method === 'kpay'): ?>
                                        <span class="badge badge-info text-xs block mt-1">💳 KPay</span>
                                    <?php elseif($payment->method === 'manual'): ?>
                                        <span class="badge badge-info text-xs block mt-1">🏦 Manual Bank</span>
                                    <?php elseif($payment->method === 'wavepay'): ?>
                                        <span class="badge badge-info text-xs block mt-1">📱 WavePay</span>
                                    <?php else: ?>
                                        <span class="badge badge-info text-xs block mt-1"><?php echo e(ucfirst($payment->method)); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <p class="text-light-text text-sm"><?php echo e($transaction->description ?? 'Transaction'); ?></p>
                            <?php if($isRejected && $payment->rejection_reason): ?>
                                <p class="text-red-400 text-xs mt-1">Reason: <?php echo e($payment->rejection_reason); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-bold <?php echo e($transaction->type === 'credit' ? 'text-secondary' : 'text-red-400'); ?>">
                                <?php echo e($transaction->type === 'credit' ? '+' : '-'); ?><?php echo e(number_format($transaction->amount)); ?> Ks
                            </p>
                        </td>
                        <td class="py-3 px-4 text-light-text text-sm">
                            <?php echo e(number_format($transaction->balance_after)); ?> Ks
                        </td>
                        <td class="py-3 px-4">
                            <?php if($payment): ?>
                                <?php if($payment->status === 'rejected'): ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php elseif($payment->status === 'pending'): ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php elseif($payment->status === 'approved'): ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge badge-info">Completed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <span class="text-4xl block mb-2">💳</span>
                            <p>No transactions found</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            <?php echo e($transactions->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/admin/transactions/index.blade.php ENDPATH**/ ?>
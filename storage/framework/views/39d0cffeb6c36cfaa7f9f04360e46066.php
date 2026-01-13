

<?php $__env->startSection('title', 'Users Management - Admin Panel'); ?>
<?php $__env->startSection('page-title', 'Users Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Search and Filters -->
    <div class="card">
        <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search"
                       value="<?php echo e(request('search')); ?>"
                       placeholder="Search by name, phone, email..." 
                       class="input-field"
                       id="searchInput">
            </div>
            <button type="submit" class="btn-primary px-6 py-3">Search</button>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn-outline px-6 py-3">Reset</a>
        </form>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-border">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">User</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Phone</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Email</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Balance</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Orders</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-dark-border hover:bg-dark-base transition-colors">
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                                    <span class="text-lg">👤</span>
                                </div>
                                <div>
                                    <p class="text-light-text font-semibold"><?php echo e($user->name ?? 'Guest'); ?></p>
                                    <?php if($user->telegram_username): ?>
                                        <p class="text-gray-400 text-xs">{{ $user->telegram_username }}</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-light-text"><?php echo e($user->phone ?? 'N/A'); ?></td>
                        <td class="py-3 px-4 text-gray-400"><?php echo e($user->email ?? 'N/A'); ?></td>
                        <td class="py-3 px-4">
                            <span class="text-secondary font-semibold"><?php echo e(number_format($user->balance ?? 0)); ?> Ks</span>
                        </td>
                        <td class="py-3 px-4 text-light-text"><?php echo e(number_format($user->orders_count ?? 0)); ?></td>
                        <td class="py-3 px-4">
                            <?php if($user->is_blocked ?? false): ?>
                                <span class="badge badge-danger">Blocked</span>
                            <?php else: ?>
                                <span class="badge badge-success">Active</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-2">
                                <button onclick="adjustBalance(<?php echo e($user->id); ?>)" class="text-primary hover:text-primary-light text-sm">Balance</button>
                                <button onclick="viewOrders(<?php echo e($user->id); ?>)" class="text-secondary hover:text-secondary-light text-sm">Orders</button>
                                <button onclick="toggleBlock(<?php echo e($user->id); ?>, <?php echo e($user->is_blocked ? 'false' : 'true'); ?>)" 
                                        class="text-red-400 hover:text-red-300 text-sm">
                                    <?php echo e($user->is_blocked ? 'Unblock' : 'Block'); ?>

                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">No users found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($users->hasPages()): ?>
        <div class="mt-6 flex items-center justify-between">
            <p class="text-gray-400 text-sm">
                Showing <?php echo e($users->firstItem() ?? 0); ?> to <?php echo e($users->lastItem() ?? 0); ?> of <?php echo e($users->total()); ?> users
            </p>
            <div class="flex space-x-2">
                <?php if($users->onFirstPage()): ?>
                    <button class="btn-outline px-4 py-2 text-sm opacity-50 cursor-not-allowed" disabled>Previous</button>
                <?php else: ?>
                    <a href="<?php echo e($users->previousPageUrl()); ?>" class="btn-outline px-4 py-2 text-sm">Previous</a>
                <?php endif; ?>
                
                <?php if($users->hasMorePages()): ?>
                    <a href="<?php echo e($users->nextPageUrl()); ?>" class="btn-outline px-4 py-2 text-sm">Next</a>
                <?php else: ?>
                    <button class="btn-outline px-4 py-2 text-sm opacity-50 cursor-not-allowed" disabled>Next</button>
                <?php endif; ?>
            </div>
        </div>
        <?php elseif($users->total() > 0): ?>
        <div class="mt-6">
            <p class="text-gray-400 text-sm">
                Showing <?php echo e($users->total()); ?> user(s)
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function adjustBalance(userId) {
    const amount = prompt('Enter amount to adjust (positive to add, negative to deduct):');
    if (amount !== null && amount !== '') {
        fetch(`/admin/users/${userId}/balance`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ amount: parseFloat(amount) })
        }).then(() => location.reload());
    }
}

function viewOrders(userId) {
    window.location.href = `/admin/orders?user_id=${userId}`;
}

function toggleBlock(userId, block) {
    if (confirm(block ? 'Block this user?' : 'Unblock this user?')) {
        fetch(`/admin/users/${userId}/block`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ block: block === 'true' })
        }).then(() => location.reload());
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/admin/users/index.blade.php ENDPATH**/ ?>
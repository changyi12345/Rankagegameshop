

<?php $__env->startSection('title', 'Auto TopUp Products - RanKage Game Shop'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-4">
    <h1 class="text-2xl font-bold text-light-text mb-6 flex items-center">
        <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
        Auto TopUp Products
    </h1>

    <!-- Game Filter -->
    <div class="mb-4">
        <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-hide">
            <a href="<?php echo e(route('products.index')); ?>" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors <?php echo e(!$gameFilter ? 'bg-primary text-white' : 'bg-dark-base text-gray-400 hover:text-light-text'); ?>">
                All Games
            </a>
            <a href="<?php echo e(route('products.index', ['game' => 'mlbb'])); ?>" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors <?php echo e($gameFilter == 'mlbb' ? 'bg-primary text-white' : 'bg-dark-base text-gray-400 hover:text-light-text'); ?>">
                ⚔️ Mobile Legends
            </a>
            <a href="<?php echo e(route('products.index', ['game' => 'pubg'])); ?>" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors <?php echo e($gameFilter == 'pubg' ? 'bg-primary text-white' : 'bg-dark-base text-gray-400 hover:text-light-text'); ?>">
                🎯 PUBG Mobile
            </a>
        </div>
    </div>

    <!-- Categories Filter -->
    <?php if(!empty($categories)): ?>
    <div class="mb-6">
        <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-hide">
            <a href="<?php echo e(route('products.index', $gameFilter ? ['game' => $gameFilter] : [])); ?>" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors <?php echo e(!$categoryId ? 'bg-dark-card text-light-text border border-primary' : 'bg-dark-base text-gray-400 hover:text-light-text'); ?>">
                All Categories
            </a>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('products.index', array_merge(['category_id' => $category['id']], $gameFilter ? ['game' => $gameFilter] : []))); ?>" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors <?php echo e($categoryId == $category['id'] ? 'bg-dark-card text-light-text border border-primary' : 'bg-dark-base text-gray-400 hover:text-light-text'); ?>">
                <?php echo e($category['title']); ?>

                <span class="ml-2 text-xs opacity-75">(<?php echo e($category['product_count'] ?? 0); ?>)</span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php $__empty_1 = true; $__currentLoopData = $products ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $title = strtolower($product['title'] ?? '');
            $category = strtolower($product['category_title'] ?? '');
            $isMLBB = stripos($title, 'diamond') !== false || stripos($category, 'mobile legends') !== false || stripos($category, 'mlbb') !== false;
            $isPUBG = stripos($title, 'uc') !== false || stripos($category, 'pubg') !== false;
            $priceUsd = $product['unit_price'] ?? 0;
            $priceKs = $priceUsd * ($exchangeRate ?? 2100);
        ?>
        <div class="card hover:bg-dark-base transition-colors">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <h3 class="font-bold text-light-text"><?php echo e($product['title'] ?? 'Product'); ?></h3>
                        <?php if($isMLBB): ?>
                        <span class="badge badge-warning text-xs">MLBB</span>
                        <?php elseif($isPUBG): ?>
                        <span class="badge badge-info text-xs">PUBG</span>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($product['description'])): ?>
                    <p class="text-xs text-gray-400 mb-2"><?php echo e($product['description']); ?></p>
                    <?php endif; ?>
                    <?php if(!empty($product['category_title'])): ?>
                    <span class="badge badge-info text-xs"><?php echo e($product['category_title']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Price</p>
                    <p class="text-xl font-bold text-secondary">
                        <?php echo e(number_format($priceKs)); ?> Ks
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        $<?php echo e(number_format($priceUsd, 2)); ?>

                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 mb-1">Stock</p>
                    <p class="text-lg font-semibold <?php echo e(($product['stock'] ?? 0) > 0 ? 'text-green-400' : 'text-red-400'); ?>">
                        <?php echo e(number_format($product['stock'] ?? 0)); ?>

                    </p>
                </div>
            </div>

            <a href="<?php echo e(route('products.show', $product['id'])); ?>" 
               class="btn-primary w-full py-2 text-sm text-center block">
                View Details
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full card text-center py-12">
            <span class="text-5xl mb-4 block">📦</span>
            <h3 class="text-xl font-bold text-light-text mb-2">No products found</h3>
            <p class="text-gray-400">Products will appear here when available</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\RanKeagegmshop\resources\views/user/products/index.blade.php ENDPATH**/ ?>
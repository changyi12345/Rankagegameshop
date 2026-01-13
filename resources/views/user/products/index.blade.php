@extends('layouts.user')

@section('title', 'Auto TopUp Products - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4">
    <h1 class="text-2xl font-bold text-light-text mb-6 flex items-center">
        <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
        Auto TopUp Products
    </h1>

    <!-- Game Filter -->
    <div class="mb-4">
        <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-hide">
            <a href="{{ route('products.index') }}" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors {{ !$gameFilter ? 'bg-primary text-white' : 'bg-dark-base text-gray-400 hover:text-light-text' }}">
                All Games
            </a>
            <a href="{{ route('products.index', ['game' => 'mlbb']) }}" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors {{ $gameFilter == 'mlbb' ? 'bg-primary text-white' : 'bg-dark-base text-gray-400 hover:text-light-text' }}">
                ⚔️ Mobile Legends
            </a>
            <a href="{{ route('products.index', ['game' => 'pubg']) }}" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors {{ $gameFilter == 'pubg' ? 'bg-primary text-white' : 'bg-dark-base text-gray-400 hover:text-light-text' }}">
                🎯 PUBG Mobile
            </a>
        </div>
    </div>

    <!-- Categories Filter -->
    @if(!empty($categories))
    <div class="mb-6">
        <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-hide">
            <a href="{{ route('products.index', $gameFilter ? ['game' => $gameFilter] : []) }}" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors {{ !$categoryId ? 'bg-dark-card text-light-text border border-primary' : 'bg-dark-base text-gray-400 hover:text-light-text' }}">
                All Categories
            </a>
            @foreach($categories as $category)
            <a href="{{ route('products.index', array_merge(['category_id' => $category['id']], $gameFilter ? ['game' => $gameFilter] : [])) }}" 
               class="px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-colors {{ $categoryId == $category['id'] ? 'bg-dark-card text-light-text border border-primary' : 'bg-dark-base text-gray-400 hover:text-light-text' }}">
                {{ $category['title'] }}
                <span class="ml-2 text-xs opacity-75">({{ $category['product_count'] ?? 0 }})</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($products ?? [] as $product)
        @php
            $title = strtolower($product['title'] ?? '');
            $category = strtolower($product['category_title'] ?? '');
            $isMLBB = stripos($title, 'diamond') !== false || stripos($category, 'mobile legends') !== false || stripos($category, 'mlbb') !== false;
            $isPUBG = stripos($title, 'uc') !== false || stripos($category, 'pubg') !== false;
            $priceUsd = $product['unit_price'] ?? 0;
            $priceKs = $priceUsd * ($exchangeRate ?? 2100);
        @endphp
        <div class="card hover:bg-dark-base transition-colors">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <h3 class="font-bold text-light-text">{{ $product['title'] ?? 'Product' }}</h3>
                        @if($isMLBB)
                        <span class="badge badge-warning text-xs">MLBB</span>
                        @elseif($isPUBG)
                        <span class="badge badge-info text-xs">PUBG</span>
                        @endif
                    </div>
                    @if(!empty($product['description']))
                    <p class="text-xs text-gray-400 mb-2">{{ $product['description'] }}</p>
                    @endif
                    @if(!empty($product['category_title']))
                    <span class="badge badge-info text-xs">{{ $product['category_title'] }}</span>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Price</p>
                    <p class="text-xl font-bold text-secondary">
                        {{ number_format($priceKs) }} Ks
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        ${{ number_format($priceUsd, 2) }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 mb-1">Stock</p>
                    <p class="text-lg font-semibold {{ ($product['stock'] ?? 0) > 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ number_format($product['stock'] ?? 0) }}
                    </p>
                </div>
            </div>

            <a href="{{ route('products.show', $product['id']) }}" 
               class="btn-primary w-full py-2 text-sm text-center block">
                View Details
            </a>
        </div>
        @empty
        <div class="col-span-full card text-center py-12">
            <span class="text-5xl mb-4 block">📦</span>
            <h3 class="text-xl font-bold text-light-text mb-2">No products found</h3>
            <p class="text-gray-400">Products will appear here when available</p>
        </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
@endpush
@endsection

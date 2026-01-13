@props(['paginator'])

@if($paginator->hasPages())
<div class="flex items-center justify-between mt-6">
    <div class="text-gray-400 text-sm">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </div>
    
    <div class="flex items-center space-x-2">
        <!-- Previous Button -->
        @if($paginator->onFirstPage())
        <button disabled class="btn-outline px-4 py-2 text-sm opacity-50 cursor-not-allowed">
            Previous
        </button>
        @else
        <a href="{{ $paginator->previousPageUrl() }}" class="btn-outline px-4 py-2 text-sm">
            Previous
        </a>
        @endif

        <!-- Page Numbers -->
        <div class="hidden sm:flex items-center space-x-1">
            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if($page == $paginator->currentPage())
                <span class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-semibold">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="bg-dark-base text-gray-400 hover:text-light-text px-4 py-2 rounded-xl text-sm font-semibold transition-colors">{{ $page }}</a>
                @endif
            @endforeach
        </div>

        <!-- Next Button -->
        @if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="btn-outline px-4 py-2 text-sm">
            Next
        </a>
        @else
        <button disabled class="btn-outline px-4 py-2 text-sm opacity-50 cursor-not-allowed">
            Next
        </button>
        @endif
    </div>
</div>
@endif

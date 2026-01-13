@props(['icon' => '📦', 'title' => 'No data found', 'message' => '', 'action' => null, 'actionLabel' => ''])

<div class="card text-center py-12">
    <span class="text-5xl mb-4 block">{{ $icon }}</span>
    <h3 class="text-xl font-bold text-light-text mb-2">{{ $title }}</h3>
    @if($message)
    <p class="text-gray-400 mb-6">{{ $message }}</p>
    @endif
    @if($action)
    <a href="{{ $action }}" class="btn-primary inline-block px-6 py-3">{{ $actionLabel ?: 'Get Started' }}</a>
    @endif
</div>

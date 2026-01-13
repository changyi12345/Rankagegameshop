@props(['status'])

@php
$statusConfig = [
    'completed' => ['class' => 'badge-success', 'label' => 'Success'],
    'success' => ['class' => 'badge-success', 'label' => 'Success'],
    'pending' => ['class' => 'badge-warning', 'label' => 'Pending'],
    'processing' => ['class' => 'badge-info', 'label' => 'Processing'],
    'failed' => ['class' => 'badge-danger', 'label' => 'Failed'],
    'active' => ['class' => 'badge-success', 'label' => 'Active'],
    'inactive' => ['class' => 'badge-danger', 'label' => 'Inactive'],
    'blocked' => ['class' => 'badge-danger', 'label' => 'Blocked'],
];
$config = $statusConfig[strtolower($status)] ?? ['class' => 'badge-info', 'label' => ucfirst($status)];
@endphp

<span class="badge {{ $config['class'] }}">{{ $config['label'] }}</span>

@props(['status'])

@php
    $enum = $status instanceof \App\Enums\AssetStatus ? $status : \App\Enums\AssetStatus::from($status);
    $class = match ($enum) {
        \App\Enums\AssetStatus::Active => 'status-active',
        \App\Enums\AssetStatus::Maintenance => 'status-maintenance',
        \App\Enums\AssetStatus::Warehouse => 'status-warehouse',
    };
@endphp

<span {{ $attributes->merge(['class' => "status-badge {$class}"]) }}>
    {{ $enum->value }}
</span>

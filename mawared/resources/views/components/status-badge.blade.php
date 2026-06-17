@props(['status'])

@php
    $label = match (true) {
        $status instanceof \App\Enums\AssetStatus => $status->label(),
        $status instanceof \App\Enums\MaintenanceStatus => $status->label(),
        $status instanceof \App\Enums\MaintenancePriority => $status->label(),
        default => \App\Enums\AssetStatus::from($status)->label(),
    };

    $class = match (true) {
        $status instanceof \App\Enums\AssetStatus => match ($status) {
            \App\Enums\AssetStatus::Active => 'status-active',
            \App\Enums\AssetStatus::Maintenance => 'status-maintenance',
            \App\Enums\AssetStatus::Warehouse => 'status-warehouse',
        },
        $status instanceof \App\Enums\MaintenancePriority => 'status-maintenance',
        default => 'status-warehouse',
    };
@endphp

<span {{ $attributes->merge(['class' => "status-badge {$class}"]) }}>
    {{ $label }}
</span>

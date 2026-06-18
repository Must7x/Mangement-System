@extends('layouts.app')

@section('title', __('pages.dashboard'))

@section('content')
    <x-page-header
        title="{{ __('pages.dashboard') }}"
        subtitle="{{ __('pages.dashboard_subtitle') }}"
    />

    <div class="stats-grid">
        <div class="stat-card stat-total">
            <div>
                <p class="stat-label">{{ __('messages.dashboard.stats.total_assets') }}</p>
                <p class="stat-value">{{ $stats['total'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-cubes"></i></div>
        </div>
        <div class="stat-card stat-warehouse">
            <div>
                <p class="stat-label">{{ __('messages.dashboard.stats.in_warehouse') }}</p>
                <p class="stat-value">{{ $stats['warehouse'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-warehouse"></i></div>
        </div>
        <div class="stat-card stat-active">
            <div>
                <p class="stat-label">{{ __('messages.dashboard.stats.active_assets') }}</p>
                <p class="stat-value">{{ $stats['active'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        </div>
        <div class="stat-card stat-maintenance">
            <div>
                <p class="stat-label">{{ __('messages.dashboard.stats.in_maintenance') }}</p>
                <p class="stat-value">{{ $stats['maintenance'] }}</p>
                <p style="font-size:0.75rem;margin:0.25rem 0 0;opacity:0.85;">{{ __('messages.dashboard.stats.open_requests', ['count' => $stats['open_maintenances']]) }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(10rem,1fr));gap:0.75rem;margin-bottom:1.5rem;">
        @if (auth()->user()->hasPermission('assets.view'))
        <a href="{{ route('inventory.index') }}" class="card" style="padding:1rem;text-decoration:none;color:inherit;text-align:center;transition:box-shadow 0.2s;">
            <i class="fa-solid fa-warehouse" style="font-size:1.5rem;color:var(--color-mtnima-green);margin-bottom:0.5rem;"></i>
            <p style="margin:0;font-weight:600;font-size:0.875rem;">{{ __('messages.dashboard.quick_links.inventory') }}</p>
        </a>
        @endif
        @if (auth()->user()->hasPermission('assignments.view'))
        <a href="{{ route('assignments.index') }}" class="card" style="padding:1rem;text-decoration:none;color:inherit;text-align:center;">
            <i class="fa-solid fa-handshake" style="font-size:1.5rem;color:var(--color-mtnima-green);margin-bottom:0.5rem;"></i>
            <p style="margin:0;font-weight:600;font-size:0.875rem;">{{ __('messages.dashboard.quick_links.assignments') }}</p>
        </a>
        @endif
        @if (auth()->user()->hasPermission('reports.view'))
        <a href="{{ route('reports.index') }}" class="card" style="padding:1rem;text-decoration:none;color:inherit;text-align:center;">
            <i class="fa-solid fa-chart-column" style="font-size:1.5rem;color:var(--color-mtnima-green);margin-bottom:0.5rem;"></i>
            <p style="margin:0;font-weight:600;font-size:0.875rem;">{{ __('messages.dashboard.quick_links.reports') }}</p>
        </a>
        @endif
        @if (auth()->user()->hasPermission('assets.create'))
        <a href="{{ route('assets.create') }}" class="card" style="padding:1rem;text-decoration:none;color:inherit;text-align:center;">
            <i class="fa-solid fa-plus" style="font-size:1.5rem;color:var(--color-mtnima-gold);margin-bottom:0.5rem;"></i>
            <p style="margin:0;font-weight:600;font-size:0.875rem;">{{ __('messages.dashboard.quick_links.add_asset') }}</p>
        </a>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">{{ __('messages.dashboard.recent_assets.title') }}</h3>
            <a href="{{ route('inventory.index') }}" class="link-action">{{ __('actions.view_all') }}</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('tables.device_name') }}</th>
                        <th>{{ __('tables.type') }}</th>
                        <th>{{ __('tables.serial_number') }}</th>
                        <th>{{ __('tables.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentAssets as $asset)
                        <tr>
                            <td><strong>{{ $asset->name }}</strong></td>
                            <td>{{ $asset->typeLabel() }}</td>
                            <td><span class="serial-badge">{{ $asset->serial_number }}</span></td>
                            <td><x-status-badge :status="$asset->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <p>{{ __('messages.empty.no_assets_yet') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', __('pages.reports'))

@section('content')
    <x-page-header
        title="{{ __('pages.reports') }}"
        subtitle="{{ __('pages.reports_subtitle') }}"
    />

    <div class="stats-grid">
        <div class="stat-card stat-total">
            <div>
                <p class="stat-label">{{ __('messages.reports.stats.total_assets') }}</p>
                <p class="stat-value">{{ $stats['total'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-cubes"></i></div>
        </div>
        <div class="stat-card stat-active">
            <div>
                <p class="stat-label">{{ __('messages.reports.stats.active_assignments') }}</p>
                <p class="stat-value">{{ $stats['assignments'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-file-signature"></i></div>
        </div>
        <div class="stat-card stat-warehouse">
            <div>
                <p class="stat-label">{{ __('messages.reports.stats.available_for_assignment') }}</p>
                <p class="stat-value">{{ $stats['warehouse'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-warehouse"></i></div>
        </div>
        <div class="stat-card stat-maintenance">
            <div>
                <p class="stat-label">{{ __('messages.reports.stats.in_maintenance') }}</p>
                <p class="stat-value">{{ $stats['maintenance'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(18rem,1fr));gap:1.25rem;">
        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">{{ __('messages.reports.by_type_title') }}</h3>
            </div>
            <div class="card-body" style="padding-top:0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('tables.type') }}</th>
                            <th>{{ __('tables.count') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byType as $row)
                            <tr>
                                <td>{{ \App\Models\Asset::labelForType($row->type) }}</td>
                                <td><strong>{{ $row->total }}</strong></td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">{{ __('messages.empty.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">{{ __('messages.reports.maintenance_assets_title') }}</h3>
            </div>
            <div class="card-body" style="padding-top:0;">
                @forelse ($maintenanceAssets as $asset)
                    <div style="padding:0.625rem 0;border-bottom:1px solid var(--color-border);display:flex;justify-content:space-between;align-items:center;">
                        <span>{{ $asset->name }}</span>
                        <span class="serial-badge">{{ $asset->serial_number }}</span>
                    </div>
                @empty
                    <p style="color:var(--color-muted);text-align:center;padding:1.5rem 0;">{{ __('messages.empty.no_maintenance_assets') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:1.25rem;">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">{{ __('messages.reports.recent_assignments_title') }}</h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('tables.device') }}</th>
                        <th>{{ __('fields.employee') }}</th>
                        <th>{{ __('tables.department') }}</th>
                        <th>{{ __('tables.assigned_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentAssignments as $assignment)
                        <tr>
                            <td>{{ $assignment->asset->name }}</td>
                            <td>{{ $assignment->employee_name }}</td>
                            <td>{{ $assignment->department }}</td>
                            <td>{{ $assignment->assigned_date->format('Y/m/d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">{{ __('messages.empty.no_assignments') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

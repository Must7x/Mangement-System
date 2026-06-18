@extends('layouts.app')

@section('title', __('pages.assets_show').' — '.$asset->name)

@section('content')
    <x-page-header
        :title="$asset->name"
        subtitle="{{ __('pages.assets_show_subtitle') }}"
    >
        <x-slot:actions>
            <a href="{{ route('inventory.index') }}" class="btn btn-ghost">
                <i class="fa-solid fa-arrow-right"></i> {{ __('actions.inventory_back') }}
            </a>
            @if (auth()->user()->hasPermission('assets.update'))
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary">
                <i class="fa-solid fa-pen-to-square"></i> {{ __('actions.edit') }}
            </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-body">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
                <div>
                    <p style="margin:0 0 0.35rem;font-size:0.8rem;color:var(--color-muted);">{{ __('pages.assets_show') }}</p>
                    <h2 style="margin:0;font-size:1.35rem;font-weight:800;">{{ $asset->name }}</h2>
                    <span class="serial-badge" style="margin-top:0.5rem;display:inline-block;">{{ $asset->serial_number }}</span>
                </div>
                <x-status-badge :status="$asset->status" style="font-size:0.9rem;padding:0.4rem 0.85rem;" />
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">
                <i class="fa-solid fa-circle-info" style="color:var(--color-mtnima-green);margin-left:0.5rem;"></i>
                {{ __('messages.assets.sections.details') }}
            </h3>
        </div>
        <div class="card-body">
            <dl style="display:grid;grid-template-columns:repeat(auto-fit,minmax(12rem,1fr));gap:1rem 1.5rem;margin:0;">
                <div>
                    <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.asset_name') }}</dt>
                    <dd style="margin:0;font-weight:600;">{{ $asset->name }}</dd>
                </div>
                <div>
                    <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.category') }}</dt>
                    <dd style="margin:0;font-weight:600;">{{ $asset->typeLabel() }}</dd>
                </div>
                <div>
                    <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.serial_number') }}</dt>
                    <dd style="margin:0;"><span class="serial-badge">{{ $asset->serial_number }}</span></dd>
                </div>
                <div>
                    <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.current_status') }}</dt>
                    <dd style="margin:0;"><x-status-badge :status="$asset->status" /></dd>
                </div>
                <div>
                    <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.registered_at') }}</dt>
                    <dd style="margin:0;font-variant-numeric:tabular-nums;">{{ $asset->created_at->format('Y/m/d') }}</dd>
                </div>
                <div>
                    <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.updated_at') }}</dt>
                    <dd style="margin:0;font-variant-numeric:tabular-nums;">{{ $asset->updated_at->format('Y/m/d') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">
                <i class="fa-solid fa-handshake" style="color:var(--color-mtnima-green);margin-left:0.5rem;"></i>
                {{ __('messages.assets.sections.custody') }}
            </h3>
        </div>
        <div class="card-body">
            @if ($asset->status === \App\Enums\AssetStatus::Active && $asset->assignment)
                <dl style="display:grid;grid-template-columns:repeat(auto-fit,minmax(12rem,1fr));gap:1rem 1.5rem;margin:0;">
                    <div>
                        <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.employee') }}</dt>
                        <dd style="margin:0;font-weight:600;">{{ $asset->assignment->employee?->name ?? $asset->assignment->employee_name }}</dd>
                    </div>
                    <div>
                        <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.department') }}</dt>
                        <dd style="margin:0;">{{ $asset->assignment->employee?->department?->name ?? $asset->assignment->department }}</dd>
                    </div>
                    <div>
                        <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.assigned_date') }}</dt>
                        <dd style="margin:0;font-variant-numeric:tabular-nums;">{{ $asset->assignment->assigned_date->format('Y/m/d') }}</dd>
                    </div>
                </dl>
                <div style="margin-top:1rem;">
                    <a href="{{ route('assignments.receipt', $asset->assignment) }}"
                       target="_blank"
                       class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-print"></i> {{ __('actions.print_receipt') }}
                    </a>
                </div>
            @elseif ($asset->status === \App\Enums\AssetStatus::Maintenance && $asset->openMaintenance)
                <dl style="display:grid;grid-template-columns:repeat(auto-fit,minmax(12rem,1fr));gap:1rem 1.5rem;margin:0;">
                    <div>
                        <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.fault_description') }}</dt>
                        <dd style="margin:0;">{{ $asset->openMaintenance->issue_description }}</dd>
                    </div>
                    <div>
                        <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.technician') }}</dt>
                        <dd style="margin:0;font-weight:600;">{{ $asset->openMaintenance->technician_name }}</dd>
                    </div>
                    <div>
                        <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.priority') }}</dt>
                        <dd style="margin:0;"><x-status-badge :status="$asset->openMaintenance->priority" /></dd>
                    </div>
                    <div>
                        <dt style="font-size:0.75rem;color:var(--color-muted);margin-bottom:0.25rem;">{{ __('fields.maintenance_start_date') }}</dt>
                        <dd style="margin:0;font-variant-numeric:tabular-nums;">{{ $asset->openMaintenance->maintenance_start_date->format('Y/m/d') }}</dd>
                    </div>
                </dl>
            @else
                <p style="margin:0;color:var(--color-muted);">{{ __('messages.empty.no_active_custody_or_maintenance') }}</p>
            @endif
        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">
                <i class="fa-solid fa-clock-rotate-left" style="color:var(--color-mtnima-green);margin-left:0.5rem;"></i>
                {{ __('messages.assets.sections.history') }}
            </h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('fields.employee') }}</th>
                        <th>{{ __('fields.department') }}</th>
                        <th>{{ __('fields.assigned_date') }}</th>
                        <th>{{ __('tables.returned_date') }}</th>
                        <th>{{ __('tables.duration') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignmentHistories as $history)
                        <tr>
                            <td>{{ $history->employee?->name ?? $history->employee_name }}</td>
                            <td>{{ $history->employee?->department?->name ?? $history->department_name ?? __('common.em_dash') }}</td>
                            <td><span style="font-variant-numeric:tabular-nums;">{{ $history->assigned_date->format('Y/m/d') }}</span></td>
                            <td>
                                @if ($history->returned_date)
                                    <span style="font-variant-numeric:tabular-nums;">{{ $history->returned_date->format('Y/m/d') }}</span>
                                @else
                                    <span style="color:var(--color-muted);">{{ __('common.em_dash') }}</span>
                                @endif
                            </td>
                            <td><strong>{{ $history->durationLabel() }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <p>{{ __('messages.empty.no_custody_history_for_asset') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">
                <i class="fa-solid fa-screwdriver-wrench" style="color:var(--color-mtnima-green);margin-left:0.5rem;"></i>
                {{ __('messages.assets.sections.maintenance') }}
            </h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('fields.fault_description') }}</th>
                        <th>{{ __('tables.priority') }}</th>
                        <th>{{ __('tables.technician') }}</th>
                        <th>{{ __('tables.status') }}</th>
                        <th>{{ __('tables.start_date') }}</th>
                        <th>{{ __('tables.end_date') }}</th>
                        <th>{{ __('tables.duration') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maintenances as $maintenance)
                        <tr>
                            <td style="max-width:14rem;">{{ Str::limit($maintenance->issue_description, 60) }}</td>
                            <td><x-status-badge :status="$maintenance->priority" /></td>
                            <td>{{ $maintenance->technician_name }}</td>
                            <td><x-status-badge :status="$maintenance->status" /></td>
                            <td><span style="font-variant-numeric:tabular-nums;">{{ $maintenance->maintenance_start_date->format('Y/m/d') }}</span></td>
                            <td>
                                @if ($maintenance->maintenance_end_date)
                                    <span style="font-variant-numeric:tabular-nums;">{{ $maintenance->maintenance_end_date->format('Y/m/d') }}</span>
                                @else
                                    <span style="color:var(--color-muted);">{{ __('common.em_dash') }}</span>
                                @endif
                            </td>
                            <td><strong>{{ $maintenance->durationLabel() }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fa-solid fa-screwdriver-wrench"></i>
                                    <p>{{ __('messages.empty.no_maintenance_history_for_asset') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (auth()->user()->hasPermission('activity_log.view'))
    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">
                <i class="fa-solid fa-list-check" style="color:var(--color-mtnima-green);margin-left:0.5rem;"></i>
                {{ __('messages.assets.sections.activity') }}
            </h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('tables.created_at') }}</th>
                        <th>{{ __('tables.performed_by') }}</th>
                        <th>{{ __('tables.role') }}</th>
                        <th>{{ __('tables.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activityLogs as $log)
                        <tr>
                            <td><span style="font-variant-numeric:tabular-nums;">{{ $log->created_at->format('Y/m/d H:i') }}</span></td>
                            <td><strong>{{ $log->user_name }}</strong></td>
                            <td>{{ $log->user_role }}</td>
                            <td>{{ $log->description() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="fa-solid fa-list-check"></i>
                                    <p>{{ __('messages.empty.no_activity_logs_for_asset') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection

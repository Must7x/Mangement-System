@extends('layouts.app')

@section('title', __('pages.maintenances'))

@section('content')
    <x-page-header
        title="{{ __('pages.maintenances') }}"
        subtitle="تتبع أعطال الأجهزة وعمليات الإصلاح"
    >
        <x-slot:actions>
            <a href="{{ route('maintenances.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> {{ __('actions.add_maintenance') }}
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                <i class="fa-solid fa-screwdriver-wrench" style="color:var(--color-mtnima-green);"></i>
                سجل الصيانة
            </h3>
            <form method="GET" action="{{ route('maintenances.index') }}" class="search-bar" style="flex-wrap:wrap;">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                       placeholder="بحث..."
                       class="form-input">
                <select name="status" class="form-select" style="width:auto;min-width:9rem;">
                    <option value="">كل الحالات</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>
                            {{ $status->value }}
                        </option>
                    @endforeach
                </select>
                <select name="priority" class="form-select" style="width:auto;min-width:8rem;">
                    <option value="">كل الأولويات</option>
                    @foreach ($priorities as $priority)
                        <option value="{{ $priority->value }}" @selected(($filters['priority'] ?? '') === $priority->value)>
                            {{ $priority->value }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if (!empty(array_filter($filters ?? [])))
                    <a href="{{ route('maintenances.index') }}" class="btn btn-ghost btn-sm">{{ __('actions.reset') }}</a>
                @endif
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الجهاز</th>
                        <th>وصف العطل</th>
                        <th>الأولوية</th>
                        <th>الفني</th>
                        <th>الحالة</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th>المدة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maintenances as $maintenance)
                        <tr>
                            <td>
                                @if ($maintenance->asset)
                                    <strong>{{ $maintenance->asset->name }}</strong>
                                    <span class="serial-badge" style="display:block;margin-top:0.2rem;">{{ $maintenance->asset->serial_number }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="max-width:14rem;">{{ Str::limit($maintenance->issue_description, 60) }}</td>
                            <td><span class="status-badge status-maintenance">{{ $maintenance->priority->value }}</span></td>
                            <td>{{ $maintenance->technician_name }}</td>
                            <td>
                                @if ($maintenance->isOpen())
                                    <span class="status-badge status-active">{{ $maintenance->status->value }}</span>
                                @else
                                    <span class="status-badge status-warehouse">{{ $maintenance->status->value }}</span>
                                @endif
                            </td>
                            <td><span style="font-variant-numeric:tabular-nums;">{{ $maintenance->maintenance_start_date->format('Y/m/d') }}</span></td>
                            <td>
                                @if ($maintenance->maintenance_end_date)
                                    <span style="font-variant-numeric:tabular-nums;">{{ $maintenance->maintenance_end_date->format('Y/m/d') }}</span>
                                @else
                                    <span style="color:var(--color-muted);">—</span>
                                @endif
                            </td>
                            <td><strong>{{ $maintenance->durationLabel() }}</strong></td>
                            <td>
                                @if ($maintenance->isOpen())
                                    <a href="{{ route('maintenances.edit', $maintenance) }}" class="link-action">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fa-solid fa-screwdriver-wrench"></i>
                                    <p>لا توجد سجلات صيانة حتى الآن.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($maintenances->hasPages())
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--color-border);">
                {{ $maintenances->links() }}
            </div>
        @endif
    </div>
@endsection

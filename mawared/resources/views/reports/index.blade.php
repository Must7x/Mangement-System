@extends('layouts.app')

@section('title', __('pages.reports'))

@section('content')
    <x-page-header
        title="{{ __('pages.reports') }}"
        subtitle="ملخص إحصائي عن المخزون والتخصيصات"
    />

    <div class="stats-grid">
        <div class="stat-card stat-total">
            <div>
                <p class="stat-label">إجمالي العتاد</p>
                <p class="stat-value">{{ $stats['total'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-cubes"></i></div>
        </div>
        <div class="stat-card stat-active">
            <div>
                <p class="stat-label">عهود نشطة</p>
                <p class="stat-value">{{ $stats['assignments'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-file-signature"></i></div>
        </div>
        <div class="stat-card stat-warehouse">
            <div>
                <p class="stat-label">متاح للتخصيص</p>
                <p class="stat-value">{{ $stats['warehouse'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-warehouse"></i></div>
        </div>
        <div class="stat-card stat-maintenance">
            <div>
                <p class="stat-label">تحت الصيانة</p>
                <p class="stat-value">{{ $stats['maintenance'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(18rem,1fr));gap:1.25rem;">
        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">التوزيع حسب النوع</h3>
            </div>
            <div class="card-body" style="padding-top:0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>النوع</th>
                            <th>العدد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byType as $row)
                            <tr>
                                <td>{{ $row->type }}</td>
                                <td><strong>{{ $row->total }}</strong></td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">أجهزة تحت الصيانة</h3>
            </div>
            <div class="card-body" style="padding-top:0;">
                @forelse ($maintenanceAssets as $asset)
                    <div style="padding:0.625rem 0;border-bottom:1px solid var(--color-border);display:flex;justify-content:space-between;align-items:center;">
                        <span>{{ $asset->name }}</span>
                        <span class="serial-badge">{{ $asset->serial_number }}</span>
                    </div>
                @empty
                    <p style="color:var(--color-muted);text-align:center;padding:1.5rem 0;">لا توجد أجهزة تحت الصيانة.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:1.25rem;">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">آخر عمليات التخصيص</h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الجهاز</th>
                        <th>الموظف</th>
                        <th>القسم</th>
                        <th>التاريخ</th>
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
                        <tr><td colspan="4" class="empty-state">لا توجد تخصيصات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

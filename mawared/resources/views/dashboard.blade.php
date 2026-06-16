@extends('layouts.app')

@section('title', __('pages.dashboard'))

@section('content')
    <x-page-header
        title="{{ __('pages.dashboard') }}"
        subtitle="نظرة عامة سريعة على المستودع والعمليات"
    />

    <div class="stats-grid">
        <div class="stat-card stat-total">
            <div>
                <p class="stat-label">إجمالي المعدات</p>
                <p class="stat-value">{{ $stats['total'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-cubes"></i></div>
        </div>
        <div class="stat-card stat-warehouse">
            <div>
                <p class="stat-label">في المخزن</p>
                <p class="stat-value">{{ $stats['warehouse'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-warehouse"></i></div>
        </div>
        <div class="stat-card stat-active">
            <div>
                <p class="stat-label">المعدات النشطة</p>
                <p class="stat-value">{{ $stats['active'] }}</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        </div>
        <div class="stat-card stat-maintenance">
            <div>
                <p class="stat-label">تحت الصيانة</p>
                <p class="stat-value">{{ $stats['maintenance'] }}</p>
                <p style="font-size:0.75rem;margin:0.25rem 0 0;opacity:0.85;">{{ $stats['open_maintenances'] }} طلب مفتوح</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(10rem,1fr));gap:0.75rem;margin-bottom:1.5rem;">
        <a href="{{ route('inventory.index') }}" class="card" style="padding:1rem;text-decoration:none;color:inherit;text-align:center;transition:box-shadow 0.2s;">
            <i class="fa-solid fa-warehouse" style="font-size:1.5rem;color:var(--color-mtnima-green);margin-bottom:0.5rem;"></i>
            <p style="margin:0;font-weight:600;font-size:0.875rem;">إدارة المخزون</p>
        </a>
        <a href="{{ route('assignments.index') }}" class="card" style="padding:1rem;text-decoration:none;color:inherit;text-align:center;">
            <i class="fa-solid fa-handshake" style="font-size:1.5rem;color:var(--color-mtnima-green);margin-bottom:0.5rem;"></i>
            <p style="margin:0;font-weight:600;font-size:0.875rem;">إدارة التخصيص</p>
        </a>
        <a href="{{ route('reports.index') }}" class="card" style="padding:1rem;text-decoration:none;color:inherit;text-align:center;">
            <i class="fa-solid fa-chart-column" style="font-size:1.5rem;color:var(--color-mtnima-green);margin-bottom:0.5rem;"></i>
            <p style="margin:0;font-weight:600;font-size:0.875rem;">التقارير</p>
        </a>
        <a href="{{ route('assets.create') }}" class="card" style="padding:1rem;text-decoration:none;color:inherit;text-align:center;">
            <i class="fa-solid fa-plus" style="font-size:1.5rem;color:var(--color-mtnima-gold);margin-bottom:0.5rem;"></i>
            <p style="margin:0;font-weight:600;font-size:0.875rem;">إضافة عتاد</p>
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">آخر المعدات المسجّلة</h3>
            <a href="{{ route('inventory.index') }}" class="link-action">عرض الكل</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الجهاز</th>
                        <th>النوع</th>
                        <th>S/N</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentAssets as $asset)
                        <tr>
                            <td><strong>{{ $asset->name }}</strong></td>
                            <td>{{ $asset->type }}</td>
                            <td><span class="serial-badge">{{ $asset->serial_number }}</span></td>
                            <td><x-status-badge :status="$asset->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <p>لا توجد معدات بعد.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

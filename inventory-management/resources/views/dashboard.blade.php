@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
    <x-page-header
        title="لوحة التحكم الرئيسية"
        subtitle="نظرة شاملة على المستودع وحالة العتاد"
    >
        <x-slot:actions>
            <a href="{{ route('assets.create') }}" class="btn btn-accent">
                <i class="fa-solid fa-plus"></i> إضافة عتاد
            </a>
        </x-slot:actions>
    </x-page-header>

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
            </div>
            <div class="stat-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                <i class="fa-solid fa-table-list" style="color:var(--color-primary-light);"></i>
                جرد العتاد الحالي
            </h3>
            <form method="GET" action="{{ route('dashboard') }}" class="search-bar">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                       placeholder="بحث..."
                       class="form-input">
                <select name="status" class="form-select" style="width:auto;min-width:8rem;">
                    <option value="">كل الحالات</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>
                            {{ $status->value }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if (!empty($filters['q']) || !empty($filters['status']))
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">إعادة تعيين</a>
                @endif
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الجهاز</th>
                        <th>النوع</th>
                        <th>الرقم التسلسلي</th>
                        <th>الحالة</th>
                        <th>المستلم / القسم</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        <tr>
                            <td>
                                <strong>{{ $asset->name }}</strong>
                            </td>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:0.35rem;">
                                    <i class="fa-solid fa-tag" style="color:var(--color-muted);font-size:0.75rem;"></i>
                                    {{ $asset->type }}
                                </span>
                            </td>
                            <td><span class="serial-badge">{{ $asset->serial_number }}</span></td>
                            <td><x-status-badge :status="$asset->status" /></td>
                            <td>
                                @if ($asset->status === \App\Enums\AssetStatus::Active && $asset->assignment)
                                    <div>
                                        <span style="font-weight:600;">{{ $asset->assignment->employee_name }}</span>
                                        <span style="display:block;font-size:0.75rem;color:var(--color-muted);">{{ $asset->assignment->department }}</span>
                                    </div>
                                @else
                                    <span style="color:var(--color-muted);">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('assets.edit', $asset) }}" class="link-action">
                                    <i class="fa-solid fa-pen-to-square"></i> تعديل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fa-solid fa-inbox"></i>
                                    <p>لا توجد نتائج مطابقة للبحث.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($assets->hasPages())
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--color-border);">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
@endsection

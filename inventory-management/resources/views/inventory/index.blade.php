@extends('layouts.app')

@section('title', 'إدارة المخزون')

@section('content')
    <x-page-header
        title="إدارة المخزون"
        subtitle="تسجيل وتعديل وحذف العتاد في المستودع"
    >
        <x-slot:actions>
            <a href="{{ route('assets.create') }}" class="btn btn-accent">
                <i class="fa-solid fa-plus"></i> إضافة عتاد
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                <i class="fa-solid fa-boxes-stacked" style="color:var(--color-mtnima-green);"></i>
                جرد العتاد
            </h3>
            <form method="GET" action="{{ route('inventory.index') }}" class="search-bar">
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
                    <a href="{{ route('inventory.index') }}" class="btn btn-ghost btn-sm">إعادة تعيين</a>
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
                            <td><strong>{{ $asset->name }}</strong></td>
                            <td>{{ $asset->type }}</td>
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
                                    <p>لا توجد معدات مسجّلة.</p>
                                    <a href="{{ route('assets.create') }}" class="btn btn-primary" style="margin-top:1rem;">تسجيل أول عتاد</a>
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

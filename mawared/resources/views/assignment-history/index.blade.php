@extends('layouts.app')

@section('title', 'سجل العهد التاريخي')

@section('content')
    <x-page-header
        title="سجل العهد التاريخي"
        subtitle="جميع عمليات إسناد وسحب العهد مع مدة الاستخدام"
    />

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                <i class="fa-solid fa-clock-rotate-left" style="color:var(--color-mtnima-green);"></i>
                السجل الكامل
            </h3>
            <form method="GET" action="{{ route('assignment-history.index') }}" class="search-bar">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                       placeholder="بحث بجهاز، موظف، قسم..."
                       class="form-input">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if (!empty($filters['q']))
                    <a href="{{ route('assignment-history.index') }}" class="btn btn-ghost btn-sm">إعادة تعيين</a>
                @endif
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الجهاز</th>
                        <th>الموظف</th>
                        <th>القسم</th>
                        <th>تاريخ الإسناد</th>
                        <th>تاريخ الإرجاع</th>
                        <th>المدة</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($histories as $history)
                        <tr>
                            <td>
                                @if ($history->asset)
                                    <strong>{{ $history->asset->name }}</strong>
                                    <span class="serial-badge" style="display:block;margin-top:0.2rem;">{{ $history->asset->serial_number }}</span>
                                @else
                                    <span style="color:var(--color-muted);">—</span>
                                @endif
                            </td>
                            <td>{{ $history->employee?->name ?? $history->employee_name }}</td>
                            <td>{{ $history->employee?->department?->name ?? $history->department_name }}</td>
                            <td><span style="font-variant-numeric:tabular-nums;">{{ $history->assigned_date->format('Y/m/d') }}</span></td>
                            <td>
                                @if ($history->returned_date)
                                    <span style="font-variant-numeric:tabular-nums;">{{ $history->returned_date->format('Y/m/d') }}</span>
                                @else
                                    <span style="color:var(--color-muted);">—</span>
                                @endif
                            </td>
                            <td><strong>{{ $history->durationLabel() }}</strong></td>
                            <td>
                                @if ($history->isActive())
                                    <span class="status-badge status-active">نشطة</span>
                                @else
                                    <span class="status-badge status-warehouse">مُرجَعة</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <p>لا توجد سجلات عهد حتى الآن.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($histories->hasPages())
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--color-border);">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
@endsection

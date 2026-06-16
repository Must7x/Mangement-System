@extends('layouts.app')

@section('title', __('pages.assignment_history'))

@section('content')
    <x-page-header
        title="{{ __('pages.assignment_history') }}"
        subtitle="الأرشيف الكامل لعمليات إسناد وإرجاع العهد"
    />

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                <i class="fa-solid fa-clock-rotate-left" style="color:var(--color-mtnima-green);"></i>
                سجل العهد التاريخي
            </h3>
            <form method="GET" action="{{ route('assignment-history.index') }}" class="search-bar">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                       placeholder="بحث بجهاز، موظف، قسم..."
                       class="form-input">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if (!empty($filters['q']))
                    <a href="{{ route('assignment-history.index') }}" class="btn btn-ghost btn-sm">{{ __('actions.reset') }}</a>
                @endif
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>اسم الجهاز</th>
                        <th>اسم الموظف</th>
                        <th>القسم</th>
                        <th>تاريخ الإسناد</th>
                        <th>تاريخ الإرجاع</th>
                        <th>المدة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($histories as $history)
                        <tr>
                            <td>
                                <strong>{{ $history->asset?->name ?? '—' }}</strong>
                            </td>
                            <td>{{ $history->employee?->name ?? $history->employee_name }}</td>
                            <td>{{ $history->employee?->department?->name ?? $history->department_name ?? '—' }}</td>
                            <td>
                                <span style="font-variant-numeric:tabular-nums;">{{ $history->assigned_date->format('Y/m/d') }}</span>
                            </td>
                            <td>
                                @if ($history->returned_date)
                                    <span style="font-variant-numeric:tabular-nums;">{{ $history->returned_date->format('Y/m/d') }}</span>
                                @else
                                    <span style="color:var(--color-muted);">—</span>
                                @endif
                            </td>
                            <td><strong>{{ $history->durationLabel() }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
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

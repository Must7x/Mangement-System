@extends('layouts.app')

@section('title', __('pages.assignment_history'))

@section('content')
    <x-page-header
        title="{{ __('pages.assignment_history') }}"
        subtitle="{{ __('pages.assignment_history_subtitle') }}"
    />

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                <i class="fa-solid fa-clock-rotate-left" style="color:var(--color-mtnima-green);"></i>
                {{ __('messages.assignment_history.section_title') }}
            </h3>
            <form method="GET" action="{{ route('assignment-history.index') }}" class="search-bar">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                       placeholder="{{ __('messages.assignment_history.search_placeholder') }}"
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
                        <th>{{ __('tables.device_name') }}</th>
                        <th>{{ __('tables.employee_name') }}</th>
                        <th>{{ __('tables.department') }}</th>
                        <th>{{ __('tables.assigned_date') }}</th>
                        <th>{{ __('tables.returned_date') }}</th>
                        <th>{{ __('tables.duration') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($histories as $history)
                        <tr>
                            <td>
                                <strong>{{ $history->asset?->name ?? __('common.em_dash') }}</strong>
                            </td>
                            <td>{{ $history->employee?->name ?? $history->employee_name }}</td>
                            <td>{{ $history->employee?->department?->name ?? $history->department_name ?? __('common.em_dash') }}</td>
                            <td>
                                <span style="font-variant-numeric:tabular-nums;">{{ $history->assigned_date->format('Y/m/d') }}</span>
                            </td>
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
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <p>{{ __('messages.empty.no_assignment_history') }}</p>
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

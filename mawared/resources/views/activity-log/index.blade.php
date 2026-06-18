@extends('layouts.app')

@section('title', __('pages.activity_log'))

@section('content')
    <x-page-header
        title="{{ __('pages.activity_log') }}"
        subtitle="{{ __('pages.activity_log_subtitle') }}"
    />

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                <i class="fa-solid fa-list-check" style="color:var(--color-mtnima-green);"></i>
                {{ __('messages.activity_log.section_title') }}
            </h3>
            <form method="GET" action="{{ route('activity-log.index') }}" class="search-bar">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                       placeholder="{{ __('messages.activity_log.search_placeholder') }}"
                       class="form-input">
                <select name="action" class="form-input" style="min-width:10rem;">
                    <option value="">{{ __('messages.activity_log.all_actions') }}</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action->value }}" @selected(($filters['action'] ?? '') === $action->value)>
                            {{ __('messages.activity_log.action_labels.'.$action->value) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if (!empty($filters['q']) || !empty($filters['action']))
                    <a href="{{ route('activity-log.index') }}" class="btn btn-ghost btn-sm">{{ __('actions.reset') }}</a>
                @endif
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('tables.created_at') }}</th>
                        <th>{{ __('tables.performed_by') }}</th>
                        <th>{{ __('tables.role') }}</th>
                        <th>{{ __('tables.action') }}</th>
                        <th>{{ __('tables.device') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>
                                <span style="font-variant-numeric:tabular-nums;">{{ $log->created_at->format('Y/m/d H:i') }}</span>
                            </td>
                            <td><strong>{{ $log->user_name }}</strong></td>
                            <td>{{ $log->user_role }}</td>
                            <td>{{ $log->description() }}</td>
                            <td>
                                @if ($log->asset)
                                    <a href="{{ route('assets.show', $log->asset) }}">{{ $log->asset->name }}</a>
                                @elseif (!empty($log->properties['asset_name']))
                                    {{ $log->properties['asset_name'] }}
                                @else
                                    <span style="color:var(--color-muted);">{{ __('common.em_dash') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fa-solid fa-list-check"></i>
                                    <p>{{ __('messages.empty.no_activity_logs') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--color-border);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection

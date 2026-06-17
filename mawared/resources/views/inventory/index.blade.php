@extends('layouts.app')

@section('title', __('pages.inventory'))

@section('content')
    <x-page-header
        title="{{ __('pages.inventory') }}"
        subtitle="{{ __('pages.inventory_subtitle') }}"
    >
        <x-slot:actions>
            <a href="{{ route('assets.create') }}" class="btn btn-accent">
                <i class="fa-solid fa-plus"></i> {{ __('actions.add_asset') }}
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                <i class="fa-solid fa-boxes-stacked" style="color:var(--color-mtnima-green);"></i>
                {{ __('messages.inventory.section_title') }}
            </h3>
            <form method="GET" action="{{ route('inventory.index') }}" class="search-bar">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                       placeholder="{{ __('common.search_placeholder') }}"
                       class="form-input">
                <select name="status" class="form-select" style="width:auto;min-width:8rem;">
                    <option value="">{{ __('filters.all_statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if (!empty($filters['q']) || !empty($filters['status']))
                    <a href="{{ route('inventory.index') }}" class="btn btn-ghost btn-sm">{{ __('actions.reset') }}</a>
                @endif
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('tables.device') }}</th>
                        <th>{{ __('tables.type') }}</th>
                        <th>{{ __('tables.serial_number') }}</th>
                        <th>{{ __('tables.status') }}</th>
                        <th>{{ __('tables.recipient_or_department') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        <tr>
                            <td><strong>{{ $asset->name }}</strong></td>
                            <td>{{ $asset->typeLabel() }}</td>
                            <td><span class="serial-badge">{{ $asset->serial_number }}</span></td>
                            <td><x-status-badge :status="$asset->status" /></td>
                            <td>
                                @if ($asset->status === \App\Enums\AssetStatus::Active && $asset->assignment)
                                    <div>
                                        <span style="font-weight:600;">{{ $asset->assignment->employee_name }}</span>
                                        <span style="display:block;font-size:0.75rem;color:var(--color-muted);">{{ $asset->assignment->department }}</span>
                                    </div>
                                @else
                                    <span style="color:var(--color-muted);">{{ __('common.em_dash') }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                                    <a href="{{ route('assets.show', $asset) }}" class="link-action">
                                        <i class="fa-solid fa-eye"></i> {{ __('actions.view') }}
                                    </a>
                                    <a href="{{ route('assets.edit', $asset) }}" class="link-action">
                                        <i class="fa-solid fa-pen-to-square"></i> {{ __('actions.edit') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fa-solid fa-inbox"></i>
                                    <p>{{ __('messages.empty.no_registered_assets') }}</p>
                                    <a href="{{ route('assets.create') }}" class="btn btn-primary" style="margin-top:1rem;">{{ __('actions.register_first_asset') }}</a>
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

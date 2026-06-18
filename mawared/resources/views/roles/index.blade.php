@extends('layouts.app')

@section('title', __('pages.roles'))

@section('content')
    <x-page-header
        title="{{ __('pages.roles') }}"
        subtitle="{{ __('pages.roles_subtitle') }}"
    >
        <x-slot:actions>
            @if (auth()->user()->hasPermission('roles.create'))
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> {{ __('actions.add_role') }}
                </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('tables.name') }}</th>
                        <th>{{ __('tables.slug') }}</th>
                        <th>{{ __('tables.user_count') }}</th>
                        <th>{{ __('tables.type') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td><strong>{{ $role->label() }}</strong></td>
                            <td><code>{{ $role->slug }}</code></td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                @if ($role->is_system)
                                    <span class="status-badge status-active">{{ __('common.system_role') }}</span>
                                @else
                                    <span class="status-badge">{{ __('common.custom_role') }}</span>
                                @endif
                            </td>
                            <td>
                                @if (auth()->user()->hasPermission('roles.update'))
                                    <a href="{{ route('roles.edit', $role) }}" class="link-action">
                                        <i class="fa-solid fa-pen"></i> {{ __('actions.edit') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fa-solid fa-user-shield"></i>
                                <p>{{ __('messages.empty.no_roles') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

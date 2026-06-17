@extends('layouts.app')

@section('title', __('pages.users'))

@section('content')
    <x-page-header
        title="{{ __('pages.users') }}"
        subtitle="{{ __('pages.users_subtitle') }}"
    >
        <x-slot:actions>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> {{ __('actions.add_user') }}
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('tables.user_full_name') }}</th>
                        <th>{{ __('tables.employee_number') }}</th>
                        <th>{{ __('tables.phone') }}</th>
                        <th>{{ __('tables.job_title') }}</th>
                        <th>{{ __('tables.email') }}</th>
                        <th>{{ __('tables.role') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->fullName() }}</strong>
                                @if ($user->id === auth()->id())
                                    <span class="status-badge status-active" style="margin-right:0.5rem;">{{ __('common.you') }}</span>
                                @endif
                            </td>
                            <td>{{ $user->employee_number ?? __('common.em_dash') }}</td>
                            <td>{{ $user->phone ?? __('common.em_dash') }}</td>
                            <td>{{ $user->job_title ?? __('common.em_dash') }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role->label() }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="link-action">
                                    <i class="fa-solid fa-pen"></i> {{ __('actions.edit') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

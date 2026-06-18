@extends('layouts.app')

@section('title', __('pages.employees'))

@section('content')
    <x-page-header
        title="{{ __('pages.employees') }}"
        subtitle="{{ __('pages.employees_subtitle') }}"
    >
        <x-slot:actions>
            @if (auth()->user()->hasPermission('employees.create'))
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> {{ __('actions.add_employee') }}
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
                        <th>{{ __('tables.department') }}</th>
                        <th>{{ __('tables.active_assignments') }}</th>
                        <th>{{ __('tables.created_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td><strong>{{ $employee->name }}</strong></td>
                            <td>{{ $employee->department?->name ?? __('common.unspecified') }}</td>
                            <td>{{ __('messages.employees.assignment_count', ['count' => $employee->assignments_count]) }}</td>
                            <td>{{ $employee->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if (auth()->user()->hasPermission('employees.update'))
                                <a href="{{ route('employees.edit', $employee) }}" class="link-action">
                                    <i class="fa-solid fa-pen"></i> {{ __('actions.edit') }}
                                </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fa-solid fa-user-tie"></i>
                                <p>{{ __('messages.empty.no_employees') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', __('pages.departments'))

@section('content')
    <x-page-header
        title="{{ __('pages.departments') }}"
        subtitle="{{ __('pages.departments_subtitle') }}"
    >
        <x-slot:actions>
            @if (auth()->user()->hasPermission('departments.create'))
            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> {{ __('actions.add_department') }}
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
                        <th>{{ __('tables.employee_count') }}</th>
                        <th>{{ __('tables.created_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $department)
                        <tr>
                            <td><strong>{{ $department->name }}</strong></td>
                            <td>{{ __('messages.departments.employee_count', ['count' => $department->employees_count ?? $department->employees()->count()]) }}</td>
                            <td>{{ $department->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if (auth()->user()->hasPermission('departments.update'))
                                <div style="display:flex;gap:0.75rem;">
                                    <a href="{{ route('departments.edit', $department) }}" class="link-action">
                                        <i class="fa-solid fa-pen"></i> {{ __('actions.edit') }}
                                    </a>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fa-solid fa-building"></i>
                                <p>{{ __('messages.empty.no_departments') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
    <x-page-header
        title="إدارة المستخدمين"
        subtitle="حسابات المسؤول التقني وأمين المخزن"
    >
        <x-slot:actions>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> مستخدم جديد
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد</th>
                        <th>الدور</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if ($user->id === auth()->id())
                                    <span class="status-badge status-active" style="margin-right:0.5rem;">أنت</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role->label() }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="link-action">
                                    <i class="fa-solid fa-pen"></i> تعديل
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

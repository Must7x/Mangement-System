@extends('layouts.app')

@section('title', 'إدارة الموظفين')

@section('content')
    <x-page-header
        title="إدارة الموظفين"
        subtitle="إدارة الموظفين وربطهم بالأقسام لاستخدامهم في العهد"
    >
        <x-slot:actions>
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> موظف جديد
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>القسم</th>
                        <th>العهد النشطة</th>
                        <th>تاريخ الإنشاء</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td><strong>{{ $employee->name }}</strong></td>
                            <td>{{ $employee->department?->name ?? 'غير محدد' }}</td>
                            <td>{{ $employee->assignments_count }} عهدة</td>
                            <td>{{ $employee->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('employees.edit', $employee) }}" class="link-action">
                                    <i class="fa-solid fa-pen"></i> تعديل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fa-solid fa-user-tie"></i>
                                <p>لا يوجد موظفون مسجلون في النظام بعد.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

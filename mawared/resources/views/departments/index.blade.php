@extends('layouts.app')

@section('title', 'إدارة الأقسام')

@section('content')
    <x-page-header
        title="إدارة الأقسام"
        subtitle="إدارة الأقسام والإدارات في الهيكل التنظيمي للوزارة"
    >
        <x-slot:actions>
            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> قسم جديد
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>عدد الموظفين</th>
                        <th>تاريخ الإنشاء</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $department)
                        <tr>
                            <td><strong>{{ $department->name }}</strong></td>
                            <td>{{ $department->employees_count ?? $department->employees()->count() }} موظف</td>
                            <td>{{ $department->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div style="display:flex;gap:0.75rem;">
                                    <a href="{{ route('departments.edit', $department) }}" class="link-action">
                                        <i class="fa-solid fa-pen"></i> تعديل
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fa-solid fa-building"></i>
                                <p>لا توجد أقسام مسجلة في النظام بعد.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

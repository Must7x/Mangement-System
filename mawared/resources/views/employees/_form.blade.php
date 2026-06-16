<div style="max-width:32rem;margin:0 auto;">
    <a href="{{ route('employees.index') }}" class="link-action" style="font-size:0.875rem;margin-bottom:1rem;display:inline-flex;">
        <i class="fa-solid fa-arrow-right"></i> العودة لإدارة الموظفين
    </a>

    <x-page-header :title="$title" />

    <div class="card">
        <div class="card-body">
            @if ($departments->isEmpty())
                <div class="alert alert-warning" style="margin-bottom:1rem;">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>يجب إنشاء قسم واحد على الأقل قبل تسجيل الموظفين.</span>
                </div>
            @endif

            <form method="POST" action="{{ $action }}">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <div class="form-group">
                    <label for="name" class="form-label">اسم الموظف</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $employee?->name) }}" required class="form-input" placeholder="مثال: محمد ولد أحمد">
                </div>

                <div class="form-group">
                    <label for="department_id" class="form-label">القسم / الإدارة</label>
                    <select name="department_id" id="department_id" required class="form-select">
                        <option value="">اختر قسماً</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $employee?->department_id) == $department->id)>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" @disabled($departments->isEmpty())>
                    <i class="fa-solid fa-floppy-disk"></i> حفظ
                </button>
            </form>

            @if ($employee)
                <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟');"
                      style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--color-border);">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> حذف الموظف
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

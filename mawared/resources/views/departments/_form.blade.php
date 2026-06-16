<div style="max-width:32rem;margin:0 auto;">
    <a href="{{ route('departments.index') }}" class="link-action" style="font-size:0.875rem;margin-bottom:1rem;display:inline-flex;">
        <i class="fa-solid fa-arrow-right"></i> {{ __('actions.back_to_departments') }}
    </a>

    <x-page-header :title="$title" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $action }}">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <div class="form-group">
                    <label for="name" class="form-label">اسم القسم / الإدارة</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $department?->name) }}" required class="form-input" placeholder="مثال: مديرية المعلوماتية">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> {{ __('actions.save') }}
                </button>
            </form>

            @if ($department)
                <form method="POST" action="{{ route('departments.destroy', $department) }}"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟');"
                      style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--color-border);">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> {{ __('actions.delete') }} القسم
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

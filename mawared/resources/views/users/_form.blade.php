<div style="max-width:32rem;margin:0 auto;">
    <a href="{{ route('users.index') }}" class="link-action" style="font-size:0.875rem;margin-bottom:1rem;display:inline-flex;">
        <i class="fa-solid fa-arrow-right"></i> {{ __('actions.back_to_users') }}
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
                    <label for="name" class="form-label">الاسم</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user?->name) }}" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user?->email) }}" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">الدور</label>
                    <select name="role" id="role" required class="form-select">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $user?->role?->value) === $role->value)>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        كلمة المرور {{ $user ? '(اتركها فارغة للإبقاء على الحالية)' : '' }}
                    </label>
                    <input type="password" name="password" id="password" {{ $user ? '' : 'required' }} class="form-input">
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> {{ __('actions.save') }}
                </button>
            </form>

            @if ($user && $user->id !== auth()->id())
                <form method="POST" action="{{ route('users.destroy', $user) }}"
                      onsubmit="return confirm('حذف هذا المستخدم؟');"
                      style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--color-border);">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> {{ __('actions.delete') }} المستخدم
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

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

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(12rem,1fr));gap:1rem;">
                    <div class="form-group" style="margin:0;">
                        <label for="first_name" class="form-label">{{ __('fields.user_first_name') }}</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user?->first_name) }}" required class="form-input">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label for="last_name" class="form-label">{{ __('fields.user_last_name') }}</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user?->last_name) }}" required class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label for="employee_number" class="form-label">{{ __('fields.employee_number') }}</label>
                    <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number', $user?->employee_number) }}" class="form-input" placeholder="{{ __('fields.employee_number_placeholder') }}">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">{{ __('fields.phone') }}</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user?->phone) }}" required class="form-input" placeholder="{{ __('fields.phone_placeholder') }}">
                </div>

                <div class="form-group">
                    <label for="job_title" class="form-label">{{ __('fields.job_title') }}</label>
                    <input type="text" name="job_title" id="job_title" value="{{ old('job_title', $user?->job_title) }}" class="form-input" placeholder="{{ __('fields.job_title_placeholder') }}">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">{{ __('fields.email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user?->email) }}" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">{{ __('fields.role') }}</label>
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
                        @if ($user)
                            {{ __('fields.password_optional_hint') }}
                        @else
                            {{ __('fields.password') }}
                        @endif
                    </label>
                    <input type="password" name="password" id="password" {{ $user ? '' : 'required' }} class="form-input">
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">{{ __('fields.password_confirmation') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> {{ __('actions.save') }}
                </button>
            </form>

            @if ($user && $user->id !== auth()->id())
                <form method="POST" action="{{ route('users.destroy', $user) }}"
                      onsubmit="return confirm(@json(__('messages.confirms.delete_user')));"
                      style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--color-border);">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> {{ __('actions.delete_user') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

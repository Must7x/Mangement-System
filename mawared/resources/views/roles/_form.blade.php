<div style="max-width:48rem;margin:0 auto;">
    <a href="{{ route('roles.index') }}" class="link-action" style="font-size:0.875rem;margin-bottom:1rem;display:inline-flex;">
        <i class="fa-solid fa-arrow-right"></i> {{ __('actions.back_to_roles') }}
    </a>

    <x-page-header :title="$title" />

    @if ($role->is_system)
        <div class="alert" style="margin-bottom:1rem;background:#fffbeb;border-color:#fcd34d;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ __('messages.roles.system_role_warning') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $action }}">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <div class="form-group">
                    <label for="name" class="form-label">{{ __('fields.role_name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required class="form-input">
                </div>

                @if ($role->exists)
                    <div class="form-group">
                        <label class="form-label">{{ __('fields.slug') }}</label>
                        <input type="text" value="{{ $role->slug }}" disabled class="form-input" style="opacity:0.7;">
                    </div>
                @endif

                <div class="form-group">
                    <label for="description" class="form-label">{{ __('fields.description') }}</label>
                    <textarea name="description" id="description" rows="3" class="form-input">{{ old('description', $role->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('fields.permissions') }}</label>
                    <div style="display:flex;flex-direction:column;gap:1.25rem;">
                        @foreach ($permissionGroups as $group => $permissions)
                            <fieldset style="border:1px solid var(--color-border);border-radius:0.5rem;padding:1rem;margin:0;">
                                <legend style="padding:0 0.5rem;font-weight:700;font-size:0.875rem;">
                                    {{ __("permissions.groups.{$group}") }}
                                </legend>
                                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(14rem,1fr));gap:0.5rem;">
                                    @foreach ($permissions as $permission)
                                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;cursor:pointer;">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->id }}"
                                                   @checked(in_array($permission->id, $selectedPermissions))>
                                            <span>{{ $permission->label() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> {{ __('actions.save') }}
                </button>
            </form>

            @if ($role->exists && ! $role->is_system && auth()->user()->hasPermission('roles.delete'))
                <form method="POST" action="{{ route('roles.destroy', $role) }}"
                      onsubmit="return confirm(@json(__('messages.confirms.delete_role')));"
                      style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--color-border);">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> {{ __('actions.delete_role') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

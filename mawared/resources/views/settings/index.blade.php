@extends('layouts.app')

@section('title', __('pages.settings'))

@section('content')
    <x-page-header
        title="{{ __('pages.settings') }}"
        subtitle="{{ __('pages.settings_subtitle') }}"
    />

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(18rem,1fr));gap:1.25rem;max-width:64rem;">
        @if (auth()->user()->hasPermission('roles.view'))
            <div class="card">
                <div class="card-header">
                    <h3 style="margin:0;font-size:1rem;font-weight:700;">
                        <i class="fa-solid fa-user-shield" style="color:var(--color-mtnima-green);margin-left:0.5rem;"></i>
                        {{ __('messages.settings.role_management') }}
                    </h3>
                </div>
                <div class="card-body">
                    <p style="margin:0 0 1rem;color:var(--color-muted);font-size:0.875rem;">
                        {{ __('messages.settings.role_management_hint') }}
                    </p>
                    <a href="{{ route('roles.index') }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-user-shield"></i> {{ __('actions.manage_roles') }}
                    </a>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">
                    <i class="fa-solid fa-user" style="color:var(--color-mtnima-green);margin-left:0.5rem;"></i>
                    {{ __('messages.settings.personal_account') }}
                </h3>
            </div>
            <div class="card-body">
                <p style="margin:0 0 0.25rem;font-weight:600;">{{ $user->fullName() }}</p>
                @if ($user->job_title)
                    <p style="margin:0 0 0.35rem;color:var(--color-muted);font-size:0.8rem;">{{ $user->job_title }}</p>
                @endif
                <p style="margin:0 0 0.35rem;color:var(--color-muted);font-size:0.875rem;">{{ $user->phone ?? __('common.em_dash') }}</p>
                <p style="margin:0 0 1rem;color:var(--color-muted);font-size:0.875rem;">{{ $user->email }}</p>
                <span class="status-badge status-active">{{ $user->roleLabel() }}</span>
                <div style="margin-top:1rem;">
                    <a href="{{ route('profile.show') }}" class="btn btn-ghost btn-sm">{{ __('actions.view_profile') }}</a>
                </div>
            </div>
        </div>

        <div class="card" style="grid-column:1/-1;">
            <div class="card-header">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">
                    <i class="fa-solid fa-key" style="color:var(--color-mtnima-green);margin-left:0.5rem;"></i>
                    {{ __('messages.settings.permissions_summary') }}
                </h3>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(14rem,1fr));gap:1rem;">
                    @foreach ($permissionGroups as $group => $permissions)
                        <div style="border:1px solid var(--color-border);border-radius:0.5rem;padding:0.75rem 1rem;">
                            <p style="margin:0 0 0.5rem;font-weight:700;font-size:0.875rem;">
                                {{ __("permissions.groups.{$group}") }}
                            </p>
                            <ul style="margin:0;padding-right:1.25rem;font-size:0.8rem;color:var(--color-muted);">
                                @foreach ($permissions as $permission)
                                    <li>{{ $permission->label() }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">
                    <i class="fa-solid fa-building" style="color:var(--color-mtnima-green);margin-left:0.5rem;"></i>
                    {{ __('messages.settings.system_info') }}
                </h3>
            </div>
            <div class="card-body" style="font-size:0.875rem;">
                <p style="margin:0 0 0.5rem;"><strong>{{ __('messages.settings.app_name_label') }}</strong> {{ __('branding.app_short') }}</p>
                <p style="margin:0 0 0.5rem;"><strong>{{ __('messages.settings.organization_label') }}</strong> {{ __('branding.organization_short') }}</p>
                <p style="margin:0;"><strong>{{ __('messages.settings.language_label') }}</strong> {{ config('locales.labels.' . app()->getLocale()) }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">
                    <i class="fa-solid fa-right-from-bracket" style="color:var(--color-muted);margin-left:0.5rem;"></i>
                    {{ __('messages.settings.session') }}
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost">
                        <i class="fa-solid fa-right-from-bracket"></i> {{ __('actions.logout_full') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

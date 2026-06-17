@extends('layouts.app')

@section('title', __('pages.settings'))

@section('content')
    <x-page-header
        title="{{ __('pages.settings') }}"
        subtitle="{{ __('pages.settings_subtitle') }}"
    />

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(18rem,1fr));gap:1.25rem;max-width:48rem;">
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
                <span class="status-badge status-active">{{ $user->role->label() }}</span>
                <div style="margin-top:1rem;">
                    <a href="{{ route('profile.show') }}" class="btn btn-ghost btn-sm">{{ __('actions.view_profile') }}</a>
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

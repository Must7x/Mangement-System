@extends('layouts.app')

@section('title', __('pages.profile'))

@section('content')
    <x-page-header title="{{ __('pages.profile') }}" subtitle="{{ __('pages.profile_subtitle') }}" />

    <div style="max-width:28rem;">
        <div class="card">
            <div style="padding:2rem 1.5rem;text-align:center;border-bottom:1px solid var(--color-border);background:linear-gradient(180deg,#f0fdfa,#fff);">
                <div style="width:4.5rem;height:4.5rem;margin:0 auto 1rem;border-radius:50%;background:linear-gradient(135deg,var(--color-primary-light),var(--color-primary));color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.75rem;box-shadow:var(--shadow-md);">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3 style="margin:0;font-size:1.25rem;font-weight:700;">{{ $user->fullName() }}</h3>
                @if ($user->job_title)
                    <p style="margin:0.35rem 0 0;color:var(--color-muted);font-size:0.875rem;">{{ $user->job_title }}</p>
                @endif
                <span class="status-badge status-active" style="margin-top:0.75rem;">{{ $user->roleLabel() }}</span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:1rem;">
                @if ($user->employee_number)
                    <div>
                        <p class="form-label" style="margin-bottom:0.25rem;">{{ __('fields.employee_number') }}</p>
                        <p style="margin:0;font-weight:500;">{{ $user->employee_number }}</p>
                    </div>
                @endif
                <div>
                    <p class="form-label" style="margin-bottom:0.25rem;">{{ __('fields.phone') }}</p>
                    <p style="margin:0;font-weight:500;">{{ $user->phone ?? __('common.em_dash') }}</p>
                </div>
                <div>
                    <p class="form-label" style="margin-bottom:0.25rem;">{{ __('fields.email') }}</p>
                    <p style="margin:0;font-weight:500;">{{ $user->email }}</p>
                </div>
                <div class="alert" style="background:#f8fafc;border-color:var(--color-border);color:var(--color-text);margin:0;">
                    @if ($user->isTechnicalAdmin())
                        <i class="fa-solid fa-shield-halved" style="color:var(--color-primary-light);"></i>
                        <span>{{ __('messages.profile.permissions.admin') }}</span>
                    @elseif ($user->isInventorySupervisor())
                        <i class="fa-solid fa-user-shield" style="color:var(--color-primary-light);"></i>
                        <span>{{ __('messages.profile.permissions.supervisor') }}</span>
                    @else
                        <i class="fa-solid fa-warehouse" style="color:var(--color-primary-light);"></i>
                        <span>{{ __('messages.profile.permissions.storekeeper') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

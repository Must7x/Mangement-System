@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
    <x-page-header title="الملف الشخصي" subtitle="بيانات الحساب والصلاحيات" />

    <div style="max-width:28rem;">
        <div class="card">
            <div style="padding:2rem 1.5rem;text-align:center;border-bottom:1px solid var(--color-border);background:linear-gradient(180deg,#f0fdfa,#fff);">
                <div style="width:4.5rem;height:4.5rem;margin:0 auto 1rem;border-radius:50%;background:linear-gradient(135deg,var(--color-primary-light),var(--color-primary));color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.75rem;box-shadow:var(--shadow-md);">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3 style="margin:0;font-size:1.25rem;font-weight:700;">{{ $user->name }}</h3>
                <span class="status-badge status-active" style="margin-top:0.75rem;">{{ $user->role->label() }}</span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:1rem;">
                <div>
                    <p class="form-label" style="margin-bottom:0.25rem;">البريد الإلكتروني</p>
                    <p style="margin:0;font-weight:500;">{{ $user->email }}</p>
                </div>
                <div class="alert" style="background:#f8fafc;border-color:var(--color-border);color:var(--color-text);margin:0;">
                    @if ($user->isTechnicalAdmin())
                        <i class="fa-solid fa-shield-halved" style="color:var(--color-primary-light);"></i>
                        <span>صلاحيات كاملة: إدارة العتاد، الحذف، ومتابعة النظام.</span>
                    @else
                        <i class="fa-solid fa-warehouse" style="color:var(--color-primary-light);"></i>
                        <span>إدارة المخزن: تسجيل العتاد، تخصيص العهد، وسحب الإسناد.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ file_exists(public_path('images/mtnima-logo.png')) ? asset('images/mtnima-logo.png') : asset('images/mtnima-logo.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body>
    <div class="guest-layout">
        <div class="login-grid">
            <div class="login-hero">
                <x-app-logo style="width:7.5rem;height:auto;background:#fff;border-radius:0.75rem;padding:0.5rem;margin-bottom:1.25rem;box-shadow:0 8px 24px rgb(0 0 0 / 0.2);" alt="MTNIMA" />

                <p style="font-size:1.25rem;font-weight:800;letter-spacing:0.12em;color:var(--color-mtnima-gold);margin:0 0 0.5rem;">MTNIMA</p>

                <h1 style="font-size:1.35rem;font-weight:700;margin:0 0 0.75rem;line-height:1.5;opacity:0.95;">
                    وزارة التحول الرقمي والابتكار<br>وعصرنة الإدارة
                </h1>

                <p style="font-size:1.05rem;font-weight:600;margin:0 0 1rem;padding:0.5rem 0;border-top:1px solid rgb(255 255 255 / 0.2);border-bottom:1px solid rgb(255 255 255 / 0.2);">
                    نظام إدارة الموارد والمعدات العمومية
                </p>

                <p style="opacity:0.88;line-height:1.7;margin:0 0 1.5rem;font-size:0.9rem;">
                    منصة موحّدة لجرد العتاد، تخصيص العهد، ومتابعة دورة حياة الأصول في المستودع.
                </p>

                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.75rem;font-size:0.875rem;">
                    <li><i class="fa-solid fa-check" style="color:var(--color-mtnima-gold);margin-left:0.5rem;"></i> جرد وتتبع الأجهزة</li>
                    <li><i class="fa-solid fa-check" style="color:var(--color-mtnima-gold);margin-left:0.5rem;"></i> إسناد وسحب العهدة آلياً</li>
                    <li><i class="fa-solid fa-check" style="color:var(--color-mtnima-gold);margin-left:0.5rem;"></i> صلاحيات محددة للمستخدمين</li>
                </ul>

                <p style="margin-top:2rem;font-size:0.7rem;opacity:0.6;line-height:1.5;">
                    Ministère de la Transformation Numérique,<br>
                    de l'Innovation et de la Modernisation de l'Administration
                </p>
            </div>

            <div class="login-form-panel">
                <div style="text-align:center;margin-bottom:1.5rem;">
                    <x-app-logo style="width:4rem;height:auto;margin:0 auto 0.75rem;display:block;" alt="MTNIMA" />
                    <h2 style="font-size:1.35rem;font-weight:700;margin:0;">تسجيل الدخول</h2>
                    <p style="color:var(--color-muted);font-size:0.875rem;margin:0.35rem 0 0;">للمسؤول التقني وأمين المخزن</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-error" style="margin-bottom:1rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <div style="position:relative;">
                            <i class="fa-solid fa-envelope" style="position:absolute;right:0.875rem;top:50%;transform:translateY(-50%);color:var(--color-muted);font-size:0.85rem;"></i>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                   class="form-input" style="padding-right:2.5rem;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <div style="position:relative;">
                            <i class="fa-solid fa-lock" style="position:absolute;right:0.875rem;top:50%;transform:translateY(-50%);color:var(--color-muted);font-size:0.85rem;"></i>
                            <input type="password" name="password" id="password" required
                                   class="form-input" style="padding-right:2.5rem;">
                        </div>
                    </div>
                    <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;color:var(--color-muted);margin-bottom:1.25rem;cursor:pointer;">
                        <input type="checkbox" name="remember" style="accent-color:var(--color-mtnima-green);">
                        تذكرني على هذا الجهاز
                    </label>
                    <button type="submit" class="btn btn-primary" style="width:100%;padding:0.875rem;">
                        <i class="fa-solid fa-arrow-left-to-bracket"></i>
                        دخول إلى النظام
                    </button>
                </form>

                <div style="margin-top:1.5rem;padding:1rem;background:#f8fafc;border-radius:0.75rem;border:1px solid var(--color-border);font-size:0.75rem;color:var(--color-muted);">
                    <p style="font-weight:600;color:var(--color-text);margin:0 0 0.5rem;">حسابات تجريبية</p>
                    <p style="margin:0.2rem 0;"><code>admin@mtnima.gov.mr</code></p>
                    <p style="margin:0.2rem 0;"><code>storekeeper@mtnima.gov.mr</code></p>
                    <p style="margin:0.5rem 0 0;">كلمة المرور: <strong>password</strong></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

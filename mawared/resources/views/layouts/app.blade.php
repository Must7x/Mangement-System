<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('locales.direction.' . app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ file_exists(public_path('images/mtnima-logo.png')) ? asset('images/mtnima-logo.png') : asset('images/mtnima-logo.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    @auth
        <div class="app-shell">
            <aside class="sidebar">
                <div class="sidebar-brand sidebar-brand-compact">
                    <x-app-logo style="width:5.5rem;height:auto;max-height:6rem;object-fit:contain;background:#fff;border-radius:0.625rem;padding:0.35rem;margin:0 auto 0.75rem;display:block;box-shadow:0 4px 16px rgb(0 0 0 / 0.2);" alt="MTNIMA" />
                    <p class="mtnima-acronym">MTNIMA</p>
                    <p class="mtnima-sub">وزارة التحول الرقمي والابتكار وعصرنة الإدارة</p>
                    <p style="margin:0.75rem 0 0;font-size:0.8rem;font-weight:600;color:#fff;opacity:0.95;">
                        إدارة الموارد والمعدات
                    </p>
                </div>
                <x-sidebar-nav />
                <div class="sidebar-footer">
                    <p style="margin:0 0 0.25rem;">{{ auth()->user()->name }}</p>
                    <p style="margin:0;opacity:0.8;">{{ auth()->user()->role->label() }}</p>
                </div>
            </aside>

            <div class="app-main">
                <header class="topbar">
                    <x-ministry-logo size="sm" variant="dark" :show-text="true">
                        {{ config('app.name') }}
                    </x-ministry-logo>
                    <nav class="mobile-nav">
                        <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">{{ __('nav.dashboard_short') }}</a>
                        <a href="{{ route('inventory.index') }}" class="btn btn-ghost btn-sm">{{ __('nav.inventory_short') }}</a>
                        <a href="{{ route('assignments.index') }}" class="btn btn-ghost btn-sm">{{ __('nav.assignments_short') }}</a>
                        <a href="{{ route('assignment-history.index') }}" class="btn btn-ghost btn-sm">{{ __('nav.history_short') }}</a>
                    </nav>
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <x-locale-switcher />
                        <span class="user-chip">
                            <i class="fa-solid fa-circle-user"></i>
                            {{ auth()->user()->role->label() }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-right-from-bracket"></i> {{ __('actions.logout') }}
                            </button>
                        </form>
                    </div>
                </header>

                <div class="page-content">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <div>
                                <strong>يرجى تصحيح الأخطاء:</strong>
                                <ul style="margin:0.5rem 0 0;padding-right:1.25rem;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>

                <footer class="app-footer">
                    <x-app-logo style="height:1.75rem;opacity:0.85;vertical-align:middle;margin-left:0.5rem;" alt="" />
                    نظام إدارة الموارد والمعدات العمومية &copy; {{ date('Y') }}
                    — وزارة التحول الرقمي والابتكار وعصرنة الإدارة (MTNIMA)
                </footer>
            </div>
        </div>
    @else
        @yield('content')
    @endauth

    @stack('scripts')
</body>
</html>

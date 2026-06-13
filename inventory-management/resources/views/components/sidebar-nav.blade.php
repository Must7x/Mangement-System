@php
    $items = [
        ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'fa-gauge-high', 'label' => 'لوحة التحكم'],
        ['route' => 'inventory.index', 'pattern' => 'inventory*|assets*', 'icon' => 'fa-warehouse', 'label' => 'إدارة المخزون'],
        ['route' => 'assignments.index', 'pattern' => 'assignments*', 'icon' => 'fa-handshake', 'label' => 'إدارة التخصيص'],
        ['route' => 'reports.index', 'pattern' => 'reports*', 'icon' => 'fa-chart-column', 'label' => 'التقارير'],
    ];

    if (auth()->user()->isTechnicalAdmin()) {
        $items[] = ['route' => 'users.index', 'pattern' => 'users*', 'icon' => 'fa-users-gear', 'label' => 'إدارة المستخدمين'];
    }

    $items[] = ['route' => 'settings.index', 'pattern' => 'settings*', 'icon' => 'fa-gear', 'label' => 'الإعدادات'];
@endphp

<nav class="sidebar-nav">
    @foreach ($items as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-link {{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
            <i class="fa-solid {{ $item['icon'] }}"></i>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>

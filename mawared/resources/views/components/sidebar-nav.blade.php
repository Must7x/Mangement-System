@php
    $items = [
        ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'fa-gauge-high', 'label' => 'لوحة التحكم'],
        ['route' => 'inventory.index', 'pattern' => 'inventory*|assets*', 'icon' => 'fa-warehouse', 'label' => 'إدارة المخزون'],
        ['route' => 'assignments.index', 'pattern' => 'assignments.*', 'icon' => 'fa-handshake', 'label' => 'إدارة التخصيص'],
        ['route' => 'assignment-history.index', 'pattern' => 'assignment-history.*', 'icon' => 'fa-clock-rotate-left', 'label' => 'سجل العهد'],
        ['route' => 'reports.index', 'pattern' => 'reports*', 'icon' => 'fa-chart-column', 'label' => 'التقارير'],
        ['route' => 'departments.index', 'pattern' => 'departments*', 'icon' => 'fa-building', 'label' => 'الأقسام'],
        ['route' => 'employees.index', 'pattern' => 'employees*', 'icon' => 'fa-user-tie', 'label' => 'الموظفون'],
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

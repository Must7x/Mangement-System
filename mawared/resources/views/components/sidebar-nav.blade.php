@php
    $items = [];

    if (auth()->user()->canManageUsers()) {
        $items[] = ['route' => 'users.index', 'pattern' => 'users*', 'icon' => 'fa-users-gear', 'label' => __('nav.users')];
    }

    if (auth()->user()->canAccessSettings()) {
        $items[] = ['route' => 'settings.index', 'pattern' => 'settings*', 'icon' => 'fa-gear', 'label' => __('nav.settings')];
    }

    if (auth()->user()->canAccessOperationalModules()) {
        $items = array_merge($items, [
            ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'fa-gauge-high', 'label' => __('nav.dashboard')],
            ['route' => 'inventory.index', 'pattern' => 'inventory*|assets*', 'icon' => 'fa-warehouse', 'label' => __('nav.inventory')],
            ['route' => 'maintenances.index', 'pattern' => 'maintenances*', 'icon' => 'fa-screwdriver-wrench', 'label' => __('nav.maintenances')],
            ['route' => 'assignments.index', 'pattern' => 'assignments.*', 'icon' => 'fa-handshake', 'label' => __('nav.assignments')],
            ['route' => 'assignment-history.index', 'pattern' => 'assignment-history.*', 'icon' => 'fa-clock-rotate-left', 'label' => __('nav.assignment_history')],
            ['route' => 'reports.index', 'pattern' => 'reports*', 'icon' => 'fa-chart-column', 'label' => __('nav.reports')],
        ]);
    }

    if (auth()->user()->canManageOrgStructure()) {
        $items[] = ['route' => 'departments.index', 'pattern' => 'departments*', 'icon' => 'fa-building', 'label' => __('nav.departments')];
        $items[] = ['route' => 'employees.index', 'pattern' => 'employees*', 'icon' => 'fa-user-tie', 'label' => __('nav.employees')];
    }
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

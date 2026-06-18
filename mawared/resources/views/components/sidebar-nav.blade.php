@php
    $items = [];

    if (auth()->user()->hasPermission('users.view')) {
        $items[] = ['route' => 'users.index', 'pattern' => 'users*', 'icon' => 'fa-users-gear', 'label' => __('nav.users')];
    }

    if (auth()->user()->hasPermission('settings.view')) {
        $items[] = ['route' => 'settings.index', 'pattern' => 'settings*', 'icon' => 'fa-gear', 'label' => __('nav.settings')];
    }

    if (auth()->user()->hasPermission('roles.view')) {
        $items[] = ['route' => 'roles.index', 'pattern' => 'roles*', 'icon' => 'fa-user-shield', 'label' => __('nav.roles')];
    }

    if (auth()->user()->hasPermission('dashboard.view')) {
        $items[] = ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'fa-gauge-high', 'label' => __('nav.dashboard')];
    }

    if (auth()->user()->hasPermission('assets.view')) {
        $items[] = ['route' => 'inventory.index', 'pattern' => 'inventory*|assets*', 'icon' => 'fa-warehouse', 'label' => __('nav.inventory')];
    }

    if (auth()->user()->hasPermission('maintenance.view')) {
        $items[] = ['route' => 'maintenances.index', 'pattern' => 'maintenances*', 'icon' => 'fa-screwdriver-wrench', 'label' => __('nav.maintenances')];
    }

    if (auth()->user()->hasPermission('assignments.view')) {
        $items[] = ['route' => 'assignments.index', 'pattern' => 'assignments.*', 'icon' => 'fa-handshake', 'label' => __('nav.assignments')];
    }

    if (auth()->user()->hasPermission('assignment_history.view')) {
        $items[] = ['route' => 'assignment-history.index', 'pattern' => 'assignment-history.*', 'icon' => 'fa-clock-rotate-left', 'label' => __('nav.assignment_history')];
    }

    if (auth()->user()->hasPermission('reports.view')) {
        $items[] = ['route' => 'reports.index', 'pattern' => 'reports*', 'icon' => 'fa-chart-column', 'label' => __('nav.reports')];
    }

    if (auth()->user()->hasPermission('departments.view')) {
        $items[] = ['route' => 'departments.index', 'pattern' => 'departments*', 'icon' => 'fa-building', 'label' => __('nav.departments')];
    }

    if (auth()->user()->hasPermission('employees.view')) {
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

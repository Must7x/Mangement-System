<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission definitions (slug => group)
    |--------------------------------------------------------------------------
    */
    'definitions' => [
        'dashboard.view' => 'dashboard',

        'users.view' => 'users',
        'users.create' => 'users',
        'users.update' => 'users',
        'users.delete' => 'users',

        'settings.view' => 'settings',

        'roles.view' => 'roles',
        'roles.create' => 'roles',
        'roles.update' => 'roles',
        'roles.delete' => 'roles',

        'assets.view' => 'assets',
        'assets.create' => 'assets',
        'assets.update' => 'assets',
        'assets.delete' => 'assets',

        'assignments.view' => 'assignments',
        'assignments.create' => 'assignments',
        'assignments.return' => 'assignments',

        'assignment_history.view' => 'assignment_history',

        'maintenance.view' => 'maintenance',
        'maintenance.create' => 'maintenance',
        'maintenance.update' => 'maintenance',
        'maintenance.complete' => 'maintenance',
        'maintenance.cancel' => 'maintenance',

        'departments.view' => 'departments',
        'departments.create' => 'departments',
        'departments.update' => 'departments',
        'departments.delete' => 'departments',

        'employees.view' => 'employees',
        'employees.create' => 'employees',
        'employees.update' => 'employees',
        'employees.delete' => 'employees',

        'reports.view' => 'reports',

        'custody_receipts.view' => 'custody_receipts',
        'custody_receipts.print' => 'custody_receipts',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route name => required permission slug
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'dashboard' => 'dashboard.view',

        'users.index' => 'users.view',
        'users.create' => 'users.create',
        'users.store' => 'users.create',
        'users.edit' => 'users.update',
        'users.update' => 'users.update',
        'users.destroy' => 'users.delete',

        'settings.index' => 'settings.view',

        'roles.index' => 'roles.view',
        'roles.create' => 'roles.create',
        'roles.store' => 'roles.create',
        'roles.edit' => 'roles.update',
        'roles.update' => 'roles.update',
        'roles.destroy' => 'roles.delete',

        'inventory.index' => 'assets.view',
        'assets.create' => 'assets.create',
        'assets.store' => 'assets.create',
        'assets.show' => 'assets.view',
        'assets.edit' => 'assets.update',
        'assets.update' => 'assets.update',
        'assets.destroy' => 'assets.delete',

        'assignments.index' => 'assignments.view',
        'assignments.store' => 'assignments.create',
        'assignments.destroy' => 'assignments.return',
        'assignments.receipt' => 'custody_receipts.view',

        'assignment-history.index' => 'assignment_history.view',

        'maintenances.index' => 'maintenance.view',
        'maintenances.create' => 'maintenance.create',
        'maintenances.store' => 'maintenance.create',
        'maintenances.edit' => 'maintenance.update',
        'maintenances.update' => 'maintenance.update',
        'maintenances.complete' => 'maintenance.complete',
        'maintenances.cancel' => 'maintenance.cancel',

        'departments.index' => 'departments.view',
        'departments.create' => 'departments.create',
        'departments.store' => 'departments.create',
        'departments.edit' => 'departments.update',
        'departments.update' => 'departments.update',
        'departments.destroy' => 'departments.delete',

        'employees.index' => 'employees.view',
        'employees.create' => 'employees.create',
        'employees.store' => 'employees.create',
        'employees.edit' => 'employees.update',
        'employees.update' => 'employees.update',
        'employees.destroy' => 'employees.delete',

        'reports.index' => 'reports.view',
    ],

    /*
    |--------------------------------------------------------------------------
    | System role permission sets (by slug)
    |--------------------------------------------------------------------------
    */
    'system_roles' => [
        'technical_admin' => [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'settings.view',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
        ],
        'inventory_supervisor' => [
            'dashboard.view',
            'assets.view',
            'assets.create',
            'assets.update',
            'assets.delete',
            'assignments.view',
            'assignments.create',
            'assignments.return',
            'assignment_history.view',
            'maintenance.view',
            'maintenance.create',
            'maintenance.update',
            'maintenance.complete',
            'maintenance.cancel',
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',
            'employees.view',
            'employees.create',
            'employees.update',
            'employees.delete',
            'reports.view',
            'custody_receipts.view',
            'custody_receipts.print',
        ],
        'warehouse_keeper' => [
            'dashboard.view',
            'assets.view',
            'assets.create',
            'assets.update',
            'assignments.view',
            'assignments.create',
            'assignments.return',
            'assignment_history.view',
            'maintenance.view',
            'maintenance.create',
            'maintenance.update',
            'maintenance.complete',
            'maintenance.cancel',
            'reports.view',
            'custody_receipts.view',
            'custody_receipts.print',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | System role metadata
    |--------------------------------------------------------------------------
    */
    'system_role_names' => [
        'technical_admin' => [
            'name' => 'Technical Administrator',
            'description' => 'Manages users, roles, permissions, and system settings.',
        ],
        'inventory_supervisor' => [
            'name' => 'Inventory Supervisor',
            'description' => 'Full operational access including org structure and asset deletion.',
        ],
        'warehouse_keeper' => [
            'name' => 'Warehouse Keeper',
            'description' => 'Daily operational access without asset deletion or org structure management.',
        ],
    ],

];

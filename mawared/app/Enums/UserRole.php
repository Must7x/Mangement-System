<?php

namespace App\Enums;

enum UserRole: string
{
    case TechnicalAdmin = 'technical_admin';
    case InventorySupervisor = 'inventory_supervisor';
    case WarehouseKeeper = 'warehouse_keeper';

    public function label(): string
    {
        return match ($this) {
            self::TechnicalAdmin => __('roles.technical_admin'),
            self::InventorySupervisor => __('roles.inventory_supervisor'),
            self::WarehouseKeeper => __('roles.warehouse_keeper'),
        };
    }

    public function canManageUsers(): bool
    {
        return $this === self::TechnicalAdmin;
    }

    public function canAccessSettings(): bool
    {
        return $this === self::TechnicalAdmin;
    }

    public function canAccessOperationalModules(): bool
    {
        return in_array($this, [self::InventorySupervisor, self::WarehouseKeeper], true);
    }

    public function canManageOrgStructure(): bool
    {
        return $this === self::InventorySupervisor;
    }

    public function canDeleteAssets(): bool
    {
        return $this === self::InventorySupervisor;
    }

    public function homeRouteName(): string
    {
        return match ($this) {
            self::TechnicalAdmin => 'users.index',
            default => 'dashboard',
        };
    }

    public function canAccessRoute(string $routeName): bool
    {
        if (in_array($routeName, ['profile.show', 'locale.switch'], true)) {
            return true;
        }

        if (str_starts_with($routeName, 'users.') || $routeName === 'settings.index') {
            return $this->canManageUsers();
        }

        if ($routeName === 'assets.destroy') {
            return $this->canDeleteAssets();
        }

        if (str_starts_with($routeName, 'departments.') || str_starts_with($routeName, 'employees.')) {
            return $this->canManageOrgStructure();
        }

        if (in_array($routeName, ['dashboard', 'inventory.index', 'reports.index', 'assignment-history.index'], true)) {
            return $this->canAccessOperationalModules();
        }

        foreach (['assets.', 'assignments.', 'maintenances.'] as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                return $this->canAccessOperationalModules();
            }
        }

        return true;
    }
}

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
}

<?php

namespace App\Enums;

enum UserRole: string
{
    case TechnicalAdmin = 'technical_admin';
    case WarehouseKeeper = 'warehouse_keeper';

    public function label(): string
    {
        return match ($this) {
            self::TechnicalAdmin => 'المسؤول التقني',
            self::WarehouseKeeper => 'أمين المخزن',
        };
    }
}

<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Warehouse = 'مخزن';
    case Active = 'نشط';
    case Maintenance = 'في الصيانة';

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Active => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::Maintenance => 'bg-amber-100 text-amber-800 border-amber-200',
            self::Warehouse => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Warehouse => __('enums.asset_status.warehouse'),
            self::Active => __('enums.asset_status.active'),
            self::Maintenance => __('enums.asset_status.maintenance'),
        };
    }
}

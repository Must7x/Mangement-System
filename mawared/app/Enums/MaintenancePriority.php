<?php

namespace App\Enums;

enum MaintenancePriority: string
{
    case Low = 'منخفضة';
    case Medium = 'متوسطة';
    case High = 'عالية';
    case Urgent = 'عاجلة';

    /**
     * @return list<self>
     */
    public static function options(): array
    {
        return self::cases();
    }

    public function label(): string
    {
        return match ($this) {
            self::Low => __('enums.maintenance_priority.low'),
            self::Medium => __('enums.maintenance_priority.medium'),
            self::High => __('enums.maintenance_priority.high'),
            self::Urgent => __('enums.maintenance_priority.urgent'),
        };
    }
}

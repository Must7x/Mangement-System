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
}

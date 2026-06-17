<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Pending = 'قيد الانتظار';
    case InProgress = 'قيد التنفيذ';
    case Completed = 'مكتملة';
    case Cancelled = 'ملغاة';

    /**
     * @return list<self>
     */
    public static function openStatuses(): array
    {
        return [self::Pending, self::InProgress];
    }

    /**
     * @return list<self>
     */
    public static function editableStatuses(): array
    {
        return self::openStatuses();
    }

    public function isOpen(): bool
    {
        return in_array($this, self::openStatuses(), true);
    }

    public function isClosed(): bool
    {
        return ! $this->isOpen();
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('enums.maintenance_status.pending'),
            self::InProgress => __('enums.maintenance_status.in_progress'),
            self::Completed => __('enums.maintenance_status.completed'),
            self::Cancelled => __('enums.maintenance_status.cancelled'),
        };
    }
}

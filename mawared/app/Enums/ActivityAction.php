<?php

namespace App\Enums;

enum ActivityAction: string
{
    case AssetCreated = 'asset_created';
    case AssetUpdated = 'asset_updated';
    case AssetDeleted = 'asset_deleted';
    case AssignmentCreated = 'assignment_created';
    case AssignmentReturned = 'assignment_returned';
    case MaintenanceCreated = 'maintenance_created';
    case MaintenanceUpdated = 'maintenance_updated';
    case MaintenanceCompleted = 'maintenance_completed';
    case MaintenanceCancelled = 'maintenance_cancelled';

    /**
     * @return list<self>
     */
    public static function options(): array
    {
        return self::cases();
    }
}

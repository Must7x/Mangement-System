<?php

namespace App\Services;

use App\Enums\ActivityAction;
use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public static function log(
        ActivityAction $action,
        ?Model $subject = null,
        ?Asset $asset = null,
        array $properties = [],
    ): ActivityLog {
        /** @var User|null $user */
        $user = auth()->user();

        if ($asset === null && $subject instanceof Asset) {
            $asset = $subject;
        }

        return ActivityLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->fullName() ?? __('common.unspecified'),
            'user_role' => $user?->roleLabel() ?? __('common.em_dash'),
            'action' => $action->value,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->getKey(),
            'asset_id' => $asset?->id,
            'properties' => $properties,
        ]);
    }
}

<?php

namespace App\Models;

use App\Enums\ActivityAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'subject_type',
        'subject_id',
        'asset_id',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'action' => ActivityAction::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function description(): string
    {
        $key = 'messages.activity_log.actions.'.$this->action->value;

        return __($key, $this->properties ?? []);
    }
}

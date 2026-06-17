<?php

namespace App\Models;

use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use Database\Factories\MaintenanceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    /** @use HasFactory<MaintenanceFactory> */
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'issue_description',
        'priority',
        'technician_name',
        'status',
        'maintenance_start_date',
        'maintenance_end_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'priority' => MaintenancePriority::class,
            'status' => MaintenanceStatus::class,
            'maintenance_start_date' => 'date',
            'maintenance_end_date' => 'date',
        ];
    }

    /**
     * @param  Builder<Maintenance>  $query
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(
            fn (MaintenanceStatus $status) => $status->value,
            MaintenanceStatus::openStatuses()
        ));
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function isOpen(): bool
    {
        return $this->status->isOpen();
    }

    public function isClosed(): bool
    {
        return $this->status->isClosed();
    }

    public function durationDays(): int
    {
        $end = $this->maintenance_end_date ?? now();

        return (int) $this->maintenance_start_date->diffInDays($end);
    }

    public function durationLabel(): string
    {
        $days = $this->durationDays();

        if ($this->maintenance_end_date === null) {
            return __('messages.duration.days_open', ['count' => $days]);
        }

        return __('messages.duration.days', ['count' => $days]);
    }
}

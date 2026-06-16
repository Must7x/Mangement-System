<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentHistory extends Model
{
    protected $fillable = [
        'asset_id',
        'employee_id',
        'employee_name',
        'department_name',
        'assigned_date',
        'returned_date',
    ];

    protected function casts(): array
    {
        return [
            'assigned_date' => 'date',
            'returned_date' => 'date',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function durationDays(): int
    {
        $end = $this->returned_date ?? now();

        return (int) $this->assigned_date->diffInDays($end);
    }

    public function durationLabel(): string
    {
        $days = $this->durationDays();

        if ($this->returned_date === null) {
            return $days.' يوم (نشطة)';
        }

        return $days.' يوم';
    }

    public function isActive(): bool
    {
        return $this->returned_date === null;
    }
}

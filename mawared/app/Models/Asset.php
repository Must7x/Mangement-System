<?php

namespace App\Models;

use App\Enums\AssetStatus;
use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory;
    protected $fillable = ['name', 'type', 'serial_number', 'status'];

    protected function casts(): array
    {
        return [
            'status' => AssetStatus::class,
        ];
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class, 'asset_id')->latestOfMany();
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function openMaintenance(): HasOne
    {
        return $this->hasOne(Maintenance::class)
            ->open()
            ->latestOfMany();
    }

    public function assignmentHistories(): HasMany
    {
        return $this->hasMany(AssignmentHistory::class);
    }
}

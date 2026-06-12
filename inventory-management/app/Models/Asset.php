<?php

namespace App\Models;

use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
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
}

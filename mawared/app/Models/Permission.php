<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'slug',
        'group',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    public function label(): string
    {
        $key = "permissions.{$this->slug}";

        return __($key) !== $key ? __($key) : $this->slug;
    }

    public function groupLabel(): string
    {
        $key = "permissions.groups.{$this->group}";

        return __($key) !== $key ? __($key) : $this->group;
    }
}

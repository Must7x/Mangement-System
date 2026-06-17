<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'first_name', 'last_name', 'phone', 'job_title', 'employee_number', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            $user->name = trim("{$user->first_name} {$user->last_name}");
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isTechnicalAdmin(): bool
    {
        return $this->role === UserRole::TechnicalAdmin;
    }

    public function isInventorySupervisor(): bool
    {
        return $this->role === UserRole::InventorySupervisor;
    }

    public function canManageUsers(): bool
    {
        return $this->role->canManageUsers();
    }

    public function canAccessSettings(): bool
    {
        return $this->role->canAccessSettings();
    }

    public function canAccessOperationalModules(): bool
    {
        return $this->role->canAccessOperationalModules();
    }

    public function canManageOrgStructure(): bool
    {
        return $this->role->canManageOrgStructure();
    }

    public function canDeleteAssets(): bool
    {
        return $this->role->canDeleteAssets();
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function homeRoute(): string
    {
        return route($this->role->homeRouteName());
    }

    public function canAccessRoute(string $routeName): bool
    {
        return $this->role->canAccessRoute($routeName);
    }

    public function canAccessUrl(string $url): bool
    {
        try {
            $route = app('router')->getRoutes()->match(\Illuminate\Http\Request::create($url));
            $name = $route->getName();

            return $name ? $this->canAccessRoute($name) : true;
        } catch (\Throwable) {
            return false;
        }
    }
}

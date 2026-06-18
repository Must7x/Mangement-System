<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'first_name', 'last_name', 'phone', 'job_title', 'employee_number', 'email', 'password', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string>|null */
    protected ?array $cachedPermissionSlugs = null;

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            $user->name = trim("{$user->first_name} {$user->last_name}");
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function assignedRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $slug): bool
    {
        return in_array($slug, $this->permissionSlugs(), true);
    }

    /**
     * @param  list<string>  $slugs
     */
    public function hasAnyPermission(array $slugs): bool
    {
        foreach ($slugs as $slug) {
            if ($this->hasPermission($slug)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public function permissionSlugs(): array
    {
        if ($this->cachedPermissionSlugs !== null) {
            return $this->cachedPermissionSlugs;
        }

        $role = $this->resolvedRole();

        if ($role) {
            $role->loadMissing('permissions');
            $this->cachedPermissionSlugs = $role->permissions->pluck('slug')->all();
        } else {
            $this->cachedPermissionSlugs = [];
        }

        return $this->cachedPermissionSlugs;
    }

    public function roleSlug(): ?string
    {
        return $this->resolvedRole()?->slug;
    }

    public function isTechnicalAdmin(): bool
    {
        return $this->roleSlug() === UserRole::TechnicalAdmin->value;
    }

    public function isInventorySupervisor(): bool
    {
        return $this->roleSlug() === UserRole::InventorySupervisor->value;
    }

    public function canManageUsers(): bool
    {
        return $this->hasPermission('users.view');
    }

    public function canAccessSettings(): bool
    {
        return $this->hasPermission('settings.view');
    }

    public function canAccessOperationalModules(): bool
    {
        return $this->hasAnyPermission([
            'dashboard.view',
            'assets.view',
            'assignments.view',
            'assignment_history.view',
            'maintenance.view',
            'reports.view',
        ]);
    }

    public function canManageOrgStructure(): bool
    {
        return $this->hasPermission('departments.view');
    }

    public function canDeleteAssets(): bool
    {
        return $this->hasPermission('assets.delete');
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function homeRoute(): string
    {
        if ($this->isTechnicalAdmin()) {
            return route('users.index');
        }

        return route('dashboard');
    }

    public function canAccessRoute(string $routeName): bool
    {
        if (in_array($routeName, ['profile.show', 'locale.switch'], true)) {
            return true;
        }

        $permission = config("permissions.routes.{$routeName}");

        if ($permission === null) {
            return true;
        }

        return $this->hasPermission($permission);
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

    public function roleLabel(): string
    {
        return $this->resolvedRole()?->label() ?? __('common.em_dash');
    }

    private function resolvedRole(): ?Role
    {
        if (! $this->role_id) {
            return null;
        }

        $this->loadMissing('assignedRole');

        return $this->assignedRole;
    }
}

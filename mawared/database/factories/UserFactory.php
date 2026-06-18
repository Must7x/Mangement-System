<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->numerify('+222 45 ## ## ##'),
            'job_title' => fake()->optional()->jobTitle(),
            'employee_number' => fake()->unique()->numerify('USR-###'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role_id' => fn () => self::roleId(UserRole::WarehouseKeeper->value),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function technicalAdmin(): static
    {
        return $this->state(fn () => ['role_id' => self::roleId(UserRole::TechnicalAdmin->value)]);
    }

    public function warehouseKeeper(): static
    {
        return $this->state(fn () => ['role_id' => self::roleId(UserRole::WarehouseKeeper->value)]);
    }

    public function inventorySupervisor(): static
    {
        return $this->state(fn () => ['role_id' => self::roleId(UserRole::InventorySupervisor->value)]);
    }

    private static function roleId(string $slug): int
    {
        $id = Role::query()->where('slug', $slug)->value('id');

        if (! $id) {
            throw new \RuntimeException("Role [{$slug}] not found. Run migrations first.");
        }

        return $id;
    }
}

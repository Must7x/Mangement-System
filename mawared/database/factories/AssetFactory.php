<?php

namespace Database\Factories;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement(['laptop', 'printer', 'desktop', 'monitor', 'furniture']),
            'serial_number' => strtoupper(fake()->unique()->bothify('SN-####-????')),
            'status' => AssetStatus::Warehouse,
        ];
    }

    public function warehouse(): static
    {
        return $this->state(fn () => ['status' => AssetStatus::Warehouse]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => AssetStatus::Active]);
    }

    public function maintenance(): static
    {
        return $this->state(fn () => ['status' => AssetStatus::Maintenance]);
    }
}

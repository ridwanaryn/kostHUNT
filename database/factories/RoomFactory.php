<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Building;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'name' => 'Room '.fake()->unique()->numberBetween(1, 999),
            'description' => fake()->sentence(),
            'size_sqm' => fake()->numberBetween(9, 30),
            'price_monthly' => fake()->numberBetween(500_000, 3_000_000),
            'price_yearly' => null,
            'electricity_included' => fake()->boolean(),
            'water_included' => fake()->boolean(),
            'is_active' => true,
        ];
    }
}

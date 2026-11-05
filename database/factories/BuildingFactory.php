<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Building;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->owner(),
            'name' => fake()->company().' Kost',
            'description' => fake()->paragraph(),
            'street' => fake()->streetAddress(),
            'city' => fake()->city(),
            'province' => fake()->randomElement(['Jawa Barat', 'Jawa Timur', 'DKI Jakarta', 'Bali', 'Jawa Tengah']),
            'house_rules' => fake()->sentences(2, true),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Building;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'imageable_type' => Building::class,
            'imageable_id' => Building::factory(),
            'path' => 'photos/'.Str::uuid().'.jpg',
            'is_cover' => false,
            'sort_order' => 0,
        ];
    }
}

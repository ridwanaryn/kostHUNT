<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MaintenanceStatus;
use App\Models\Booking;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceRequest>
 */
class MaintenanceRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'room_id' => Room::factory(),
            'tenant_id' => User::factory()->tenant(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => MaintenanceStatus::Open,
            'owner_note' => null,
            'resolved_at' => null,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\PeriodType;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now()->addDays(5)->startOfDay();

        return [
            'tenant_id' => User::factory()->tenant(),
            'room_id' => Room::factory(),
            'period_type' => PeriodType::Monthly,
            'duration' => 1,
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addMonthNoOverflow()->subDay()->toDateString(),
            'rent_amount' => 1_500_000,
            'status' => BookingStatus::Upcoming,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Active,
            'start_date' => Carbon::now()->subDays(5)->toDateString(),
            'end_date' => Carbon::now()->addDays(25)->toDateString(),
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Upcoming,
        ]);
    }
}

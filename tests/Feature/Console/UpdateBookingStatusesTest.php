<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateBookingStatusesTest extends TestCase
{
    use RefreshDatabase;

    public function test_upcoming_bookings_become_active_on_their_start_date(): void
    {
        $booking = $this->booking(BookingStatus::Upcoming, startInDays: 0, endInDays: 30);

        $this->artisan('kosthunt:update-booking-statuses')->assertSuccessful();

        $this->assertSame(BookingStatus::Active, $booking->fresh()->status);
    }

    public function test_active_bookings_become_completed_after_their_end_date(): void
    {
        $booking = $this->booking(BookingStatus::Active, startInDays: -10, endInDays: -1);

        $this->artisan('kosthunt:update-booking-statuses')->assertSuccessful();

        $booking->refresh();
        $this->assertSame(BookingStatus::Completed, $booking->status);
        $this->assertNotNull($booking->ended_at);
    }

    public function test_future_upcoming_bookings_stay_upcoming(): void
    {
        $booking = $this->booking(BookingStatus::Upcoming, startInDays: 5, endInDays: 35);

        $this->artisan('kosthunt:update-booking-statuses')->assertSuccessful();

        $this->assertSame(BookingStatus::Upcoming, $booking->fresh()->status);
    }

    public function test_terminated_bookings_are_not_changed(): void
    {
        $booking = $this->booking(BookingStatus::Terminated, startInDays: -10, endInDays: -1);

        $this->artisan('kosthunt:update-booking-statuses')->assertSuccessful();

        $this->assertSame(BookingStatus::Terminated, $booking->fresh()->status);
    }

    private function booking(BookingStatus $status, int $startInDays, int $endInDays): Booking
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $tenant = User::factory()->tenant()->create();

        return Booking::factory()->for($room)->create([
            'tenant_id' => $tenant->id,
            'status' => $status,
            'start_date' => now()->addDays($startInDays)->toDateString(),
            'end_date' => now()->addDays($endInDays)->toDateString(),
        ]);
    }
}

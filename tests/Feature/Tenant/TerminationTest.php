<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TerminationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_request_termination(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->bookingFor($tenant, BookingStatus::Active);
        $moveOut = now()->addDays(10)->toDateString();

        $response = $this->actingAs($tenant)->post(route('tenant.bookings.termination', $booking), [
            'requested_move_out_date' => $moveOut,
            'reason' => 'Relocating for an internship.',
        ]);

        $booking->refresh();

        $response->assertRedirect();
        $this->assertSame(BookingStatus::TerminationRequested, $booking->status);
        $this->assertNotNull($booking->termination_requested_at);
        $this->assertSame($moveOut, $booking->requested_move_out_date->toDateString());
        $this->assertSame('Relocating for an internship.', $booking->termination_reason);
    }

    public function test_move_out_date_must_be_before_the_booking_ends(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->bookingFor($tenant, BookingStatus::Active);

        $this->actingAs($tenant)->post(route('tenant.bookings.termination', $booking), [
            'requested_move_out_date' => $booking->end_date->toDateString(),
        ])->assertSessionHasErrors('requested_move_out_date');
    }

    public function test_tenant_cannot_request_termination_for_a_completed_booking(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->bookingFor($tenant, BookingStatus::Completed);

        $this->actingAs($tenant)->post(route('tenant.bookings.termination', $booking), [
            'requested_move_out_date' => now()->addDays(5)->toDateString(),
        ])->assertForbidden();
    }

    public function test_other_tenant_cannot_request_termination(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherTenant = User::factory()->tenant()->create();
        $booking = $this->bookingFor($tenant, BookingStatus::Active);

        $this->actingAs($otherTenant)->post(route('tenant.bookings.termination', $booking), [
            'requested_move_out_date' => now()->addDays(5)->toDateString(),
        ])->assertForbidden();
    }

    private function bookingFor(User $tenant, BookingStatus $status): Booking
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['is_active' => true]);

        return Booking::factory()->for($room)->create([
            'tenant_id' => $tenant->id,
            'status' => $status,
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(25)->toDateString(),
        ]);
    }
}

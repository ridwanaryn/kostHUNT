<?php

declare(strict_types=1);

namespace Tests\Feature\Owner;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TerminationDecisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_approve_a_termination_and_shorten_the_end_date(): void
    {
        [$owner, $booking] = $this->pendingTermination(startInDays: -10);
        $moveOut = $booking->requested_move_out_date?->toDateString();

        $this->actingAs($owner)->post(route('owner.bookings.termination.approve', $booking), [
            'note' => 'Approved, good luck!',
        ])->assertRedirect();

        $booking->refresh();
        $this->assertSame(BookingStatus::Terminated, $booking->status);
        $this->assertSame($moveOut, $booking->end_date->toDateString());
        $this->assertNotNull($booking->ended_at);
        $this->assertSame('Approved, good luck!', $booking->termination_owner_note);
    }

    public function test_owner_can_reject_a_termination_and_restore_active_status(): void
    {
        [$owner, $booking] = $this->pendingTermination(startInDays: -10);

        $this->actingAs($owner)->post(route('owner.bookings.termination.reject', $booking), [
            'note' => 'Please finish the term.',
        ])->assertRedirect();

        $booking->refresh();
        $this->assertSame(BookingStatus::Active, $booking->status);
        $this->assertNull($booking->termination_requested_at);
        $this->assertNull($booking->requested_move_out_date);
        $this->assertSame('Please finish the term.', $booking->termination_owner_note);
    }

    public function test_rejecting_restores_upcoming_for_a_future_booking(): void
    {
        [$owner, $booking] = $this->pendingTermination(startInDays: 5);

        $this->actingAs($owner)->post(route('owner.bookings.termination.reject', $booking));

        $this->assertSame(BookingStatus::Upcoming, $booking->fresh()->status);
    }

    public function test_other_owner_cannot_decide(): void
    {
        [, $booking] = $this->pendingTermination(startInDays: -10);
        $otherOwner = User::factory()->owner()->create();

        $this->actingAs($otherOwner)->post(route('owner.bookings.termination.approve', $booking))
            ->assertForbidden();

        $this->assertSame(BookingStatus::TerminationRequested, $booking->fresh()->status);
    }

    public function test_tenant_cannot_decide(): void
    {
        [$owner, $booking] = $this->pendingTermination(startInDays: -10);
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)->post(route('owner.bookings.termination.approve', $booking))
            ->assertForbidden();
    }

    /**
     * @return array{0: User, 1: Booking}
     */
    private function pendingTermination(int $startInDays): array
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['is_active' => true]);
        $tenant = User::factory()->tenant()->create();

        $booking = Booking::factory()->for($room)->create([
            'tenant_id' => $tenant->id,
            'status' => BookingStatus::TerminationRequested,
            'start_date' => now()->addDays($startInDays)->toDateString(),
            'end_date' => now()->addDays(40)->toDateString(),
            'termination_requested_at' => now(),
            'requested_move_out_date' => now()->addDays(15)->toDateString(),
            'termination_reason' => 'Moving out.',
        ]);

        return [$owner, $booking];
    }
}

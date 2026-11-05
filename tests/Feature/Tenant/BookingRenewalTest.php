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

class BookingRenewalTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_renew_an_active_booking_within_the_window(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->activeBooking($tenant, endInDays: 20);

        $response = $this->actingAs($tenant)->post(route('tenant.bookings.renew', $booking), [
            'period_type' => 'monthly',
            'duration' => 2,
        ]);

        $renewed = Booking::query()->where('renewed_from_id', $booking->id)->firstOrFail();

        $response->assertRedirect(route('tenant.bookings.show', $renewed));
        $this->assertSame($tenant->id, $renewed->tenant_id);
        $this->assertSame(BookingStatus::Upcoming, $renewed->status);
        $this->assertSame(
            $booking->end_date->copy()->addDay()->toDateString(),
            $renewed->start_date->toDateString(),
        );
        $this->assertSame(1, $renewed->invoices()->count());
        $this->assertSame(BookingStatus::Active, $booking->fresh()->status);
    }

    public function test_tenant_cannot_renew_outside_the_window(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->activeBooking($tenant, endInDays: 60);

        $this->actingAs($tenant)->post(route('tenant.bookings.renew', $booking), [
            'period_type' => 'monthly',
            'duration' => 1,
        ])->assertForbidden();
    }

    public function test_tenant_cannot_renew_an_upcoming_booking(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->activeBooking($tenant, endInDays: 20);
        $booking->update(['status' => BookingStatus::Upcoming]);

        $this->actingAs($tenant)->post(route('tenant.bookings.renew', $booking), [
            'period_type' => 'monthly',
            'duration' => 1,
        ])->assertForbidden();
    }

    public function test_other_tenant_cannot_renew_the_booking(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherTenant = User::factory()->tenant()->create();
        $booking = $this->activeBooking($tenant, endInDays: 20);

        $this->actingAs($otherTenant)->post(route('tenant.bookings.renew', $booking), [
            'period_type' => 'monthly',
            'duration' => 1,
        ])->assertForbidden();
    }

    public function test_owner_cannot_renew_a_booking(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->activeBooking($tenant, endInDays: 20);
        $owner = $booking->room->building->owner;

        $this->actingAs($owner)->post(route('tenant.bookings.renew', $booking), [
            'period_type' => 'monthly',
            'duration' => 1,
        ])->assertForbidden();
    }

    private function activeBooking(User $tenant, int $endInDays): Booking
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create([
            'price_monthly' => 1_500_000,
            'price_yearly' => 15_000_000,
            'is_active' => true,
        ]);

        return Booking::factory()->for($room)->create([
            'tenant_id' => $tenant->id,
            'status' => BookingStatus::Active,
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays($endInDays)->toDateString(),
        ]);
    }
}

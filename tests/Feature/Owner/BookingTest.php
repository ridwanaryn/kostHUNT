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

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_only_sees_bookings_for_their_own_rooms(): void
    {
        $owner = User::factory()->owner()->create();
        $otherOwner = User::factory()->owner()->create();

        $ownRoom = Room::factory()->for(Building::factory()->for($owner, 'owner'))->create(['name' => 'Own Room']);
        $otherRoom = Room::factory()->for(Building::factory()->for($otherOwner, 'owner'))->create(['name' => 'Other Room']);

        Booking::factory()->for($ownRoom)->create();
        Booking::factory()->for($otherRoom)->create();

        $this->actingAs($owner)
            ->get(route('owner.bookings.index'))
            ->assertOk()
            ->assertSee('Own Room')
            ->assertDontSee('Other Room');
    }

    public function test_owner_can_filter_bookings_by_status(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $activeRoom = Room::factory()->for($building)->create(['name' => 'Active Room']);
        $upcomingRoom = Room::factory()->for($building)->create(['name' => 'Upcoming Room']);

        $activeTenant = User::factory()->tenant()->create(['name' => 'Active Tenant']);
        $upcomingTenant = User::factory()->tenant()->create(['name' => 'Upcoming Tenant']);

        Booking::factory()->for($activeRoom)->active()->create(['tenant_id' => $activeTenant->id]);
        Booking::factory()->for($upcomingRoom)->upcoming()->create(['tenant_id' => $upcomingTenant->id]);

        $this->actingAs($owner)
            ->get(route('owner.bookings.index', ['status' => BookingStatus::Active->value]))
            ->assertOk()
            ->assertSee('Active Tenant')
            ->assertDontSee('Upcoming Tenant');
    }

    public function test_owner_can_view_a_booking_for_their_room(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $booking = Booking::factory()->for($room)->create();

        $this->actingAs($owner)
            ->get(route('owner.bookings.show', $booking))
            ->assertOk();
    }

    public function test_owner_cannot_view_another_owners_booking(): void
    {
        $owner = User::factory()->owner()->create();
        $otherOwner = User::factory()->owner()->create();
        $building = Building::factory()->for($otherOwner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $booking = Booking::factory()->for($room)->create();

        $this->actingAs($owner)
            ->get(route('owner.bookings.show', $booking))
            ->assertForbidden();
    }

    public function test_tenant_cannot_access_owner_booking_area(): void
    {
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)
            ->get(route('owner.bookings.index'))
            ->assertForbidden();
    }
}

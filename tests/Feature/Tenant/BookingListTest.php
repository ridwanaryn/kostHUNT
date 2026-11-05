<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Models\Booking;
use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingListTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_sees_only_their_own_bookings(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherTenant = User::factory()->tenant()->create();
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        $ownRoom = Room::factory()->for($building)->create(['name' => 'My Room']);
        $otherRoom = Room::factory()->for($building)->create(['name' => 'Other Room']);

        Booking::factory()->for($ownRoom)->create(['tenant_id' => $tenant->id]);
        Booking::factory()->for($otherRoom)->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($tenant)
            ->get(route('tenant.bookings.index'))
            ->assertOk()
            ->assertSee('My Room')
            ->assertDontSee('Other Room');
    }

    public function test_tenant_can_view_their_own_booking(): void
    {
        $tenant = User::factory()->tenant()->create();
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $booking = Booking::factory()->for($room)->create(['tenant_id' => $tenant->id]);

        $this->actingAs($tenant)
            ->get(route('tenant.bookings.show', $booking))
            ->assertOk()
            ->assertSee($booking->room->name);
    }

    public function test_tenant_cannot_view_another_tenants_booking(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherTenant = User::factory()->tenant()->create();
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $booking = Booking::factory()->for($room)->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($tenant)
            ->get(route('tenant.bookings.show', $booking))
            ->assertForbidden();
    }

    public function test_owner_cannot_access_tenant_booking_area(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get(route('tenant.bookings.index'))
            ->assertForbidden();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Owner;

use App\Models\Booking;
use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_sees_tenants_for_their_building(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $tenant = User::factory()->tenant()->create(['name' => 'Alice Tenant']);
        Booking::factory()->for($room)->create(['tenant_id' => $tenant->id]);

        $this->actingAs($owner)
            ->get(route('owner.buildings.tenants', $building))
            ->assertOk()
            ->assertSee('Alice Tenant');
    }

    public function test_other_owner_cannot_view_building_tenants(): void
    {
        $owner = User::factory()->owner()->create();
        $otherOwner = User::factory()->owner()->create();
        $building = Building::factory()->for($otherOwner, 'owner')->create();

        $this->actingAs($owner)
            ->get(route('owner.buildings.tenants', $building))
            ->assertForbidden();
    }

    public function test_tenant_cannot_view_building_tenants(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)
            ->get(route('owner.buildings.tenants', $building))
            ->assertForbidden();
    }
}

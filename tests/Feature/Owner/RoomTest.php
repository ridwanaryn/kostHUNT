<?php

declare(strict_types=1);

namespace Tests\Feature\Owner;

use App\Models\Building;
use App\Models\Facility;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_a_room_with_facilities(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $facilities = Facility::factory()->count(2)->create();

        $response = $this->actingAs($owner)->post(route('owner.rooms.store', $building), [
            'name' => 'Room A',
            'description' => 'A cozy room.',
            'size_sqm' => 12,
            'price_monthly' => 1_500_000,
            'price_yearly' => 16_000_000,
            'electricity_included' => '1',
            'water_included' => '0',
            'is_active' => '1',
            'facilities' => $facilities->pluck('id')->all(),
        ]);

        $room = Room::query()->firstWhere('name', 'Room A');

        $this->assertNotNull($room);
        $this->assertSame($building->id, $room->building_id);
        $this->assertCount(2, $room->facilities);
        $response->assertRedirect(route('owner.rooms.show', $room));
    }

    public function test_room_name_is_unique_within_a_building(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        Room::factory()->for($building)->create(['name' => 'Room A']);

        $this->actingAs($owner)->post(route('owner.rooms.store', $building), [
            'name' => 'Room A',
            'price_monthly' => 1_000_000,
        ])->assertSessionHasErrors('name');
    }

    public function test_same_room_name_is_allowed_in_a_different_building(): void
    {
        $owner = User::factory()->owner()->create();
        $first = Building::factory()->for($owner, 'owner')->create();
        $second = Building::factory()->for($owner, 'owner')->create();
        Room::factory()->for($first)->create(['name' => 'Room A']);

        $this->actingAs($owner)->post(route('owner.rooms.store', $second), [
            'name' => 'Room A',
            'price_monthly' => 1_000_000,
        ])->assertRedirect();

        $this->assertSame(1, Room::query()->where('name', 'Room A')->where('building_id', $second->id)->count());
    }

    public function test_other_owner_cannot_create_a_room(): void
    {
        $owner = User::factory()->owner()->create();
        $other = User::factory()->owner()->create();
        $building = Building::factory()->for($other, 'owner')->create();

        $this->actingAs($owner)->post(route('owner.rooms.store', $building), [
            'name' => 'Room A',
            'price_monthly' => 1_000_000,
        ])->assertForbidden();
    }

    public function test_owner_can_toggle_room_active(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['is_active' => true]);

        $this->actingAs($owner)
            ->patch(route('owner.rooms.toggle-active', $room))
            ->assertRedirect();

        $this->assertFalse($room->fresh()->is_active);
    }

    public function test_owner_can_delete_own_room(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();

        $this->actingAs($owner)
            ->delete(route('owner.rooms.destroy', $room))
            ->assertRedirect(route('owner.buildings.show', $building));

        $this->assertSoftDeleted($room);
    }

    public function test_tenant_cannot_manage_rooms(): void
    {
        $owner = User::factory()->owner()->create();
        $tenant = User::factory()->tenant()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        $this->actingAs($tenant)->post(route('owner.rooms.store', $building), [
            'name' => 'Room A',
            'price_monthly' => 1_000_000,
        ])->assertForbidden();
    }
}

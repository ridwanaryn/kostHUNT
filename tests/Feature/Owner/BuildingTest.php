<?php

declare(strict_types=1);

namespace Tests\Feature\Owner;

use App\Models\Building;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_owner_area(): void
    {
        $this->get(route('owner.buildings.index'))->assertRedirect(route('login'));
    }

    public function test_tenant_cannot_access_owner_area(): void
    {
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)
            ->get(route('owner.buildings.index'))
            ->assertForbidden();
    }

    public function test_owner_only_sees_own_buildings_in_index(): void
    {
        $owner = User::factory()->owner()->create();
        $other = User::factory()->owner()->create();
        Building::factory()->for($owner, 'owner')->create(['name' => 'Mine']);
        Building::factory()->for($other, 'owner')->create(['name' => 'Theirs']);

        $this->actingAs($owner)
            ->get(route('owner.buildings.index'))
            ->assertOk()
            ->assertSee('Mine')
            ->assertDontSee('Theirs');
    }

    public function test_owner_can_create_a_building(): void
    {
        $owner = User::factory()->owner()->create();

        $response = $this->actingAs($owner)->post(route('owner.buildings.store'), [
            'name' => 'Kost Anggrek',
            'description' => 'A quiet place to stay.',
            'street' => 'Jl. Anggrek No. 1',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'house_rules' => 'No smoking indoors.',
        ]);

        $building = Building::query()->firstWhere('name', 'Kost Anggrek');

        $this->assertNotNull($building);
        $this->assertSame($owner->id, $building->owner_id);
        $response->assertRedirect(route('owner.buildings.show', $building));
    }

    public function test_building_requires_valid_data(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->post(route('owner.buildings.store'), [])
            ->assertSessionHasErrors(['name', 'street', 'city', 'province']);
    }

    public function test_owner_can_view_own_building(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)
            ->get(route('owner.buildings.show', $building))
            ->assertOk()
            ->assertSee($building->name);
    }

    public function test_owner_cannot_view_another_owners_building(): void
    {
        $owner = User::factory()->owner()->create();
        $other = User::factory()->owner()->create();
        $building = Building::factory()->for($other, 'owner')->create();

        $this->actingAs($owner)
            ->get(route('owner.buildings.show', $building))
            ->assertForbidden();
    }

    public function test_owner_can_update_own_building(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create(['name' => 'Old Name']);

        $this->actingAs($owner)->patch(route('owner.buildings.update', $building), [
            'name' => 'New Name',
            'street' => $building->street,
            'city' => $building->city,
            'province' => $building->province,
        ])->assertRedirect(route('owner.buildings.show', $building));

        $this->assertSame('New Name', $building->fresh()->name);
    }

    public function test_owner_cannot_update_another_owners_building(): void
    {
        $owner = User::factory()->owner()->create();
        $other = User::factory()->owner()->create();
        $building = Building::factory()->for($other, 'owner')->create();

        $this->actingAs($owner)->patch(route('owner.buildings.update', $building), [
            'name' => 'Hacked',
            'street' => $building->street,
            'city' => $building->city,
            'province' => $building->province,
        ])->assertForbidden();

        $this->assertNotSame('Hacked', $building->fresh()->name);
    }

    public function test_owner_can_delete_own_building(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)
            ->delete(route('owner.buildings.destroy', $building))
            ->assertRedirect(route('owner.buildings.index'));

        $this->assertSoftDeleted($building);
    }

    public function test_owner_cannot_delete_another_owners_building(): void
    {
        $owner = User::factory()->owner()->create();
        $other = User::factory()->owner()->create();
        $building = Building::factory()->for($other, 'owner')->create();

        $this->actingAs($owner)
            ->delete(route('owner.buildings.destroy', $building))
            ->assertForbidden();

        $this->assertNotSoftDeleted($building);
    }
}

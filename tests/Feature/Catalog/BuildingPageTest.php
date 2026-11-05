<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_building_page_lists_only_active_rooms(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create(['name' => 'Kost Anggrek']);
        Room::factory()->for($building)->create(['name' => 'Visible Room', 'is_active' => true]);
        Room::factory()->for($building)->create(['name' => 'Hidden Room', 'is_active' => false]);

        $this->get(route('buildings.show', $building))
            ->assertOk()
            ->assertSee('Kost Anggrek')
            ->assertSee('Visible Room')
            ->assertDontSee('Hidden Room');
    }

    public function test_guest_can_view_building_page(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        $this->get(route('buildings.show', $building))->assertOk();
    }
}

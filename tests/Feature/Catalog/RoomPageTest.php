<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Building;
use App\Models\Facility;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_an_active_room(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create(['name' => 'Kost Melati']);
        $facility = Facility::factory()->create(['name' => 'Private Bathroom']);
        $room = Room::factory()->for($building)->create([
            'name' => 'Room 101',
            'description' => 'A bright room.',
            'price_monthly' => 1_750_000,
            'is_active' => true,
        ]);
        $room->facilities()->attach($facility);

        $this->get(route('rooms.show', $room))
            ->assertOk()
            ->assertSee('Room 101')
            ->assertSee('Kost Melati')
            ->assertSee('Private Bathroom')
            ->assertSee('Rp 1.750.000');
    }

    public function test_hidden_room_page_returns_404(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['is_active' => false]);

        $this->get(route('rooms.show', $room))->assertNotFound();
    }
}

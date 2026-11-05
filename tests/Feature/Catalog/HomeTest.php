<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_the_home_page(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_home_page_shows_only_active_rooms(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create(['city' => 'Bandung']);

        Room::factory()->for($building)->create(['name' => 'Visible Room', 'is_active' => true]);
        Room::factory()->for($building)->create(['name' => 'Hidden Room', 'is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Visible Room')
            ->assertDontSee('Hidden Room');
    }
}

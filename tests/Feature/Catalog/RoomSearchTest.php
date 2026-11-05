<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_results_only_include_active_rooms(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        Room::factory()->for($building)->create(['name' => 'Active Room', 'is_active' => true]);
        Room::factory()->for($building)->create(['name' => 'Hidden Room', 'is_active' => false]);

        $this->get(route('rooms.index'))
            ->assertOk()
            ->assertSee('Active Room')
            ->assertDontSee('Hidden Room');
    }

    public function test_search_filters_by_city(): void
    {
        $owner = User::factory()->owner()->create();
        $bandung = Building::factory()->for($owner, 'owner')->create(['city' => 'Bandung']);
        $jakarta = Building::factory()->for($owner, 'owner')->create(['city' => 'Jakarta']);

        Room::factory()->for($bandung)->create(['name' => 'Bandung Room']);
        Room::factory()->for($jakarta)->create(['name' => 'Jakarta Room']);

        $this->get(route('rooms.index', ['city' => 'Bandung']))
            ->assertOk()
            ->assertSee('Bandung Room')
            ->assertDontSee('Jakarta Room');
    }

    public function test_search_filters_by_price_range(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        Room::factory()->for($building)->create(['name' => 'Cheap Room', 'price_monthly' => 900_000]);
        Room::factory()->for($building)->create(['name' => 'Expensive Room', 'price_monthly' => 4_000_000]);

        $this->get(route('rooms.index', ['max_price' => 1_000_000]))
            ->assertOk()
            ->assertSee('Cheap Room')
            ->assertDontSee('Expensive Room');

        $this->get(route('rooms.index', ['min_price' => 2_000_000]))
            ->assertOk()
            ->assertSee('Expensive Room')
            ->assertDontSee('Cheap Room');
    }

    public function test_search_filters_by_facility(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $ac = Facility::factory()->create(['name' => 'AC']);

        $withAc = Room::factory()->for($building)->create(['name' => 'AC Room']);
        $withAc->facilities()->attach($ac);

        Room::factory()->for($building)->create(['name' => 'No AC Room']);

        $this->get(route('rooms.index', ['facilities' => [$ac->id]]))
            ->assertOk()
            ->assertSee('AC Room')
            ->assertDontSee('No AC Room');
    }

    public function test_results_can_be_sorted_by_price(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        Room::factory()->for($building)->create(['name' => 'Cheap Room', 'price_monthly' => 800_000]);
        Room::factory()->for($building)->create(['name' => 'Expensive Room', 'price_monthly' => 4_000_000]);

        $this->get(route('rooms.index', ['sort' => 'price_asc']))
            ->assertOk()
            ->assertSeeInOrder(['Cheap Room', 'Expensive Room']);

        $this->get(route('rooms.index', ['sort' => 'price_desc']))
            ->assertOk()
            ->assertSeeInOrder(['Expensive Room', 'Cheap Room']);
    }

    public function test_search_filters_by_availability_date(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $booked = Room::factory()->for($building)->create(['name' => 'Booked Room']);
        Room::factory()->for($building)->create(['name' => 'Free Room']);

        Booking::factory()->for($booked)->create([
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(40)->toDateString(),
            'status' => BookingStatus::Upcoming,
        ]);

        $this->get(route('rooms.index', ['start_date' => now()->addDays(15)->toDateString()]))
            ->assertOk()
            ->assertSee('Free Room')
            ->assertDontSee('Booked Room');

        $this->get(route('rooms.index', ['start_date' => now()->addDays(41)->toDateString()]))
            ->assertOk()
            ->assertSee('Booked Room');
    }

    public function test_invalid_sort_is_rejected(): void
    {
        $this->get(route('rooms.index', ['sort' => 'hacked']))
            ->assertSessionHasErrors('sort');
    }
}

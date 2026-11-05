<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueryEfficiencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_search_query_count_does_not_grow_with_results(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        Room::factory()->count(3)->for($building)->create(['is_active' => true]);

        $this->get(route('rooms.index'))->assertOk();

        $measure = function (): int {
            DB::flushQueryLog();
            DB::enableQueryLog();

            $this->get(route('rooms.index'))->assertOk();

            $count = count(DB::getQueryLog());
            DB::disableQueryLog();

            return $count;
        };

        $withThree = $measure();

        Room::factory()->count(9)->for($building)->create(['is_active' => true]);

        $withTwelve = $measure();

        $this->assertSame($withThree, $withTwelve);
    }
}

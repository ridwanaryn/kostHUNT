<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $room = $this->room();

        $this->post(route('tenant.favorites.store', $room))->assertRedirect(route('login'));
    }

    public function test_tenant_can_favorite_a_room(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->room();

        $this->actingAs($tenant)
            ->post(route('tenant.favorites.store', $room))
            ->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);
    }

    public function test_favoriting_twice_does_not_create_duplicates(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->room();

        $this->actingAs($tenant)->post(route('tenant.favorites.store', $room));
        $this->actingAs($tenant)->post(route('tenant.favorites.store', $room));

        $this->assertDatabaseCount('favorites', 1);
    }

    public function test_tenant_can_remove_a_favorite(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->room();
        $tenant->favoriteRooms()->attach($room->id);

        $this->actingAs($tenant)
            ->delete(route('tenant.favorites.destroy', $room))
            ->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);
    }

    public function test_favorites_page_lists_only_favorited_rooms(): void
    {
        $tenant = User::factory()->tenant()->create();
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $favorite = Room::factory()->for($building)->create(['name' => 'Favorite Room', 'is_active' => true]);
        Room::factory()->for($building)->create(['name' => 'Other Room', 'is_active' => true]);

        $tenant->favoriteRooms()->attach($favorite->id);

        $this->actingAs($tenant)
            ->get(route('tenant.favorites.index'))
            ->assertOk()
            ->assertSee('Favorite Room')
            ->assertDontSee('Other Room');
    }

    public function test_owner_cannot_favorite_a_room(): void
    {
        $owner = User::factory()->owner()->create();
        $room = $this->room();

        $this->actingAs($owner)
            ->post(route('tenant.favorites.store', $room))
            ->assertForbidden();
    }

    private function room(): Room
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        return Room::factory()->for($building)->create(['is_active' => true]);
    }
}

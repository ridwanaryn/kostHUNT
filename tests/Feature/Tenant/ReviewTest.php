<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_with_a_completed_booking_can_review(): void
    {
        [$tenant, $room, $booking] = $this->eligibleBooking(BookingStatus::Completed);

        $response = $this->actingAs($tenant)->post(route('tenant.reviews.store', $room), [
            'rating' => 5,
            'comment' => 'Great room!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'rating' => 5,
        ]);
    }

    public function test_tenant_with_an_active_booking_can_review(): void
    {
        [$tenant, $room] = $this->eligibleBooking(BookingStatus::Active);

        $this->actingAs($tenant)->post(route('tenant.reviews.store', $room), [
            'rating' => 4,
        ])->assertRedirect();

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_tenant_without_an_eligible_booking_cannot_review(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->room();

        $this->actingAs($tenant)->post(route('tenant.reviews.store', $room), [
            'rating' => 5,
        ])->assertForbidden();
    }

    public function test_a_booking_can_only_be_reviewed_once(): void
    {
        [$tenant, $room, $booking] = $this->eligibleBooking(BookingStatus::Completed);

        Review::factory()->create([
            'booking_id' => $booking->id,
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'rating' => 3,
        ]);

        $this->actingAs($tenant)->post(route('tenant.reviews.store', $room), [
            'rating' => 5,
        ])->assertForbidden();

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        [$tenant, $room] = $this->eligibleBooking(BookingStatus::Active);

        $this->actingAs($tenant)->post(route('tenant.reviews.store', $room), [
            'rating' => 6,
        ])->assertSessionHasErrors('rating');
    }

    public function test_owner_cannot_review_a_room(): void
    {
        $owner = User::factory()->owner()->create();
        $room = $this->room();

        $this->actingAs($owner)->post(route('tenant.reviews.store', $room), [
            'rating' => 5,
        ])->assertForbidden();
    }

    public function test_room_page_shows_the_average_rating_and_review_list(): void
    {
        [$tenant, $room, $booking] = $this->eligibleBooking(BookingStatus::Completed);

        Review::factory()->create([
            'booking_id' => $booking->id,
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'rating' => 4,
            'comment' => 'Nice and quiet place.',
        ]);

        $this->get(route('rooms.show', $room))
            ->assertOk()
            ->assertSee('Nice and quiet place.')
            ->assertSee('4.0');
    }

    private function room(): Room
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        return Room::factory()->for($building)->create(['is_active' => true]);
    }

    /**
     * @return array{0: User, 1: Room, 2: Booking}
     */
    private function eligibleBooking(BookingStatus $status): array
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->room();

        $booking = Booking::factory()->for($room)->create([
            'tenant_id' => $tenant->id,
            'status' => $status,
        ]);

        return [$tenant, $room, $booking];
    }
}

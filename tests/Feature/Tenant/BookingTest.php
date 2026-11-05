<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Enums\BookingStatus;
use App\Enums\InvoiceStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $room = $this->makeRoom();

        $this->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->addDays(3)->toDateString(),
            'period_type' => 'monthly',
            'duration' => 1,
        ])->assertRedirect(route('login'));
    }

    public function test_owners_cannot_book_rooms(): void
    {
        $owner = User::factory()->owner()->create();
        $room = $this->makeRoom();

        $this->actingAs($owner)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->addDays(3)->toDateString(),
            'period_type' => 'monthly',
            'duration' => 1,
        ])->assertForbidden();
    }

    public function test_tenant_can_book_an_available_room_and_first_invoice_is_created(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->makeRoom(['price_monthly' => 1_500_000]);

        $start = now()->addDays(5)->startOfDay();

        $response = $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => $start->toDateString(),
            'period_type' => 'monthly',
            'duration' => 3,
        ]);

        $booking = Booking::query()->firstOrFail();

        $response->assertRedirect(route('tenant.bookings.show', $booking));
        $this->assertSame($tenant->id, $booking->tenant_id);
        $this->assertSame(BookingStatus::Upcoming, $booking->status);
        $this->assertSame(4_500_000, $booking->rent_amount);
        $this->assertSame(
            Carbon::parse($start)->addMonthsNoOverflow(3)->subDay()->toDateString(),
            $booking->end_date->toDateString(),
        );

        $invoice = $booking->invoices()->firstOrFail();
        $this->assertSame(1_500_000, $invoice->amount);
        $this->assertSame(InvoiceStatus::Unpaid, $invoice->status);
        $this->assertSame($start->toDateString(), $invoice->period_start->toDateString());
    }

    public function test_booking_starting_today_is_active(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->makeRoom();

        $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->toDateString(),
            'period_type' => 'monthly',
            'duration' => 1,
        ]);

        $this->assertSame(BookingStatus::Active, Booking::query()->firstOrFail()->status);
    }

    public function test_overlapping_booking_is_rejected(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->makeRoom();

        Booking::factory()->for($room)->create([
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(40)->toDateString(),
            'status' => BookingStatus::Upcoming,
        ]);

        $response = $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->addDays(15)->toDateString(),
            'period_type' => 'monthly',
            'duration' => 1,
        ]);

        $response->assertSessionHasErrors('booking');
        $this->assertSame(1, Booking::query()->count());
    }

    public function test_back_to_back_booking_after_existing_one_ends_is_allowed(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->makeRoom();

        Booking::factory()->for($room)->create([
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(40)->toDateString(),
            'status' => BookingStatus::Upcoming,
        ]);

        $response = $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->addDays(41)->toDateString(),
            'period_type' => 'monthly',
            'duration' => 1,
        ]);

        $response->assertRedirect();
        $this->assertSame(2, Booking::query()->count());
    }

    public function test_start_date_cannot_be_in_the_past(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->makeRoom();

        $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->subDay()->toDateString(),
            'period_type' => 'monthly',
            'duration' => 1,
        ])->assertSessionHasErrors('start_date');
    }

    public function test_start_date_cannot_exceed_advance_window(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->makeRoom();
        $advance = (int) config('kosthunt.booking.advance_days');

        $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->addDays($advance + 10)->toDateString(),
            'period_type' => 'monthly',
            'duration' => 1,
        ])->assertSessionHasErrors('start_date');
    }

    public function test_yearly_booking_is_rejected_without_a_yearly_price(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->makeRoom(['price_yearly' => null]);

        $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->addDays(3)->toDateString(),
            'period_type' => 'yearly',
            'duration' => 1,
        ])->assertSessionHasErrors('period_type');
    }

    public function test_yearly_booking_uses_yearly_price(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->makeRoom(['price_yearly' => 15_000_000]);

        $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->addDays(3)->toDateString(),
            'period_type' => 'yearly',
            'duration' => 2,
        ]);

        $booking = Booking::query()->firstOrFail();
        $this->assertSame(30_000_000, $booking->rent_amount);
        $this->assertSame(15_000_000, $booking->invoices()->firstOrFail()->amount);
    }

    public function test_inactive_room_cannot_be_booked(): void
    {
        $tenant = User::factory()->tenant()->create();
        $room = $this->makeRoom(['is_active' => false]);

        $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->addDays(3)->toDateString(),
            'period_type' => 'monthly',
            'duration' => 1,
        ])->assertSessionHasErrors('room');
    }

    private function makeRoom(array $attributes = []): Room
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        return Room::factory()->for($building)->create(array_merge([
            'is_active' => true,
            'price_monthly' => 1_500_000,
            'price_yearly' => 15_000_000,
        ], $attributes));
    }
}

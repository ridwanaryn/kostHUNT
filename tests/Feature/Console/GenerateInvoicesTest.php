<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Enums\BookingStatus;
use App\Enums\PeriodType;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GenerateInvoicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_due_invoices_exactly_once(): void
    {
        $booking = $this->booking(
            duration: 3,
            startDate: Carbon::now()->startOfDay()->subMonths(2),
            status: BookingStatus::Active,
        );
        app(InvoiceService::class)->generateInitial($booking);

        $this->artisan('kosthunt:generate-invoices')->assertSuccessful();
        $this->assertDatabaseCount('invoices', 3);

        $this->artisan('kosthunt:generate-invoices')->assertSuccessful();
        $this->assertDatabaseCount('invoices', 3);
    }

    public function test_it_does_not_generate_far_future_invoices(): void
    {
        $booking = $this->booking(
            duration: 2,
            startDate: Carbon::now()->startOfDay()->addDays(60),
            status: BookingStatus::Upcoming,
        );
        app(InvoiceService::class)->generateInitial($booking);

        $this->artisan('kosthunt:generate-invoices')->assertSuccessful();
        $this->assertSame(1, $booking->invoices()->count());
    }

    private function booking(int $duration, Carbon $startDate, BookingStatus $status): Booking
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['price_monthly' => 1_500_000]);
        $tenant = User::factory()->tenant()->create();

        return Booking::factory()->for($room)->create([
            'tenant_id' => $tenant->id,
            'period_type' => PeriodType::Monthly,
            'duration' => $duration,
            'start_date' => $startDate->toDateString(),
            'end_date' => $startDate->copy()->addMonthsNoOverflow($duration)->subDay()->toDateString(),
            'rent_amount' => 1_500_000 * $duration,
            'status' => $status,
        ]);
    }
}

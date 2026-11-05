<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Enums\BookingStatus;
use App\Enums\InvoiceStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_dashboard_shows_bookings_and_unpaid_invoices(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['name' => 'Dashboard Room']);
        $tenant = User::factory()->tenant()->create();

        $booking = Booking::factory()->for($room)->create([
            'tenant_id' => $tenant->id,
            'status' => BookingStatus::Active,
        ]);

        Invoice::factory()->for($booking)->create([
            'invoice_number' => 'INV-DASH',
            'status' => InvoiceStatus::Unpaid,
        ]);

        $this->actingAs($tenant)
            ->get(route('tenant.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Room')
            ->assertSee('INV-DASH');
    }

    public function test_owner_cannot_access_tenant_dashboard(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get(route('tenant.dashboard'))
            ->assertForbidden();
    }
}

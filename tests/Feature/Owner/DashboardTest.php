<?php

declare(strict_types=1);

namespace Tests\Feature\Owner;

use App\Enums\BookingStatus;
use App\Enums\InvoiceStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_dashboard_shows_summary_figures(): void
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $activeRoom = Room::factory()->for($building)->create();
        $otherRoom = Room::factory()->for($building)->create();
        $tenant = User::factory()->tenant()->create();

        $activeBooking = Booking::factory()->for($activeRoom)->create([
            'tenant_id' => $tenant->id,
            'status' => BookingStatus::Active,
        ]);

        Booking::factory()->for($otherRoom)->create([
            'tenant_id' => $tenant->id,
            'status' => BookingStatus::TerminationRequested,
        ]);

        Invoice::factory()->for($activeBooking)->create([
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
            'amount' => 1_500_000,
        ]);

        Invoice::factory()->for($activeBooking)->create([
            'status' => InvoiceStatus::Unpaid,
            'amount' => 1_500_000,
        ]);

        MaintenanceRequest::factory()->create([
            'booking_id' => $activeBooking->id,
            'room_id' => $activeRoom->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($owner)
            ->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('50%')
            ->assertSee('Rp 1.500.000');
    }

    public function test_tenant_cannot_access_owner_dashboard(): void
    {
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)
            ->get(route('owner.dashboard'))
            ->assertForbidden();
    }
}

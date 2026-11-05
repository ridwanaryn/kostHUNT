<?php

declare(strict_types=1);

namespace Tests\Feature\Owner;

use App\Enums\InvoiceStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_only_sees_their_paid_invoices_in_the_report(): void
    {
        $owner = User::factory()->owner()->create();
        $otherOwner = User::factory()->owner()->create();

        $this->paidInvoice($owner, 'Kost Report', 2_000_000);
        $this->paidInvoice($otherOwner, 'Other Building', 9_000_000);

        $this->actingAs($owner)
            ->get(route('owner.reports.income', [
                'from' => now()->subMonth()->toDateString(),
                'to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Kost Report')
            ->assertSee('Rp 2.000.000')
            ->assertDontSee('Rp 9.000.000');
    }

    public function test_owner_can_filter_the_report_by_building(): void
    {
        $owner = User::factory()->owner()->create();
        $first = Building::factory()->for($owner, 'owner')->create(['name' => 'First Building']);
        $second = Building::factory()->for($owner, 'owner')->create(['name' => 'Second Building']);

        $this->paidInvoiceForBuilding($first, 1_000_000);
        $this->paidInvoiceForBuilding($second, 3_000_000);

        $this->actingAs($owner)
            ->get(route('owner.reports.income', ['building_id' => $first->id]))
            ->assertOk()
            ->assertSee('First Building')
            ->assertSee('Rp 1.000.000')
            ->assertDontSee('Rp 3.000.000');
    }

    public function test_tenant_cannot_access_income_report(): void
    {
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)
            ->get(route('owner.reports.income'))
            ->assertForbidden();
    }

    private function paidInvoice(User $owner, string $buildingName, int $amount): void
    {
        $building = Building::factory()->for($owner, 'owner')->create(['name' => $buildingName]);
        $this->paidInvoiceForBuilding($building, $amount);
    }

    private function paidInvoiceForBuilding(Building $building, int $amount): void
    {
        $room = Room::factory()->for($building)->create();
        $tenant = User::factory()->tenant()->create();
        $booking = Booking::factory()->for($room)->create(['tenant_id' => $tenant->id]);

        Invoice::factory()->for($booking)->create([
            'status' => InvoiceStatus::Paid,
            'paid_at' => now()->subDays(2),
            'amount' => $amount,
        ]);
    }
}

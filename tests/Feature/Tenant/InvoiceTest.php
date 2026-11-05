<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Models\Booking;
use App\Models\Building;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('tenant.invoices.index'))->assertRedirect(route('login'));
    }

    public function test_tenant_only_sees_their_own_invoices(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherTenant = User::factory()->tenant()->create();

        $own = $this->invoiceFor($tenant, ['invoice_number' => 'INV-OWN']);
        $other = $this->invoiceFor($otherTenant, ['invoice_number' => 'INV-OTHER']);

        $this->actingAs($tenant)
            ->get(route('tenant.invoices.index'))
            ->assertOk()
            ->assertSee($own->invoice_number)
            ->assertDontSee($other->invoice_number);
    }

    public function test_tenant_can_view_their_own_invoice(): void
    {
        $tenant = User::factory()->tenant()->create();
        $invoice = $this->invoiceFor($tenant);

        $this->actingAs($tenant)
            ->get(route('tenant.invoices.show', $invoice))
            ->assertOk()
            ->assertSee($invoice->invoice_number);
    }

    public function test_tenant_cannot_view_another_tenants_invoice(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherTenant = User::factory()->tenant()->create();
        $invoice = $this->invoiceFor($otherTenant);

        $this->actingAs($tenant)
            ->get(route('tenant.invoices.show', $invoice))
            ->assertForbidden();
    }

    public function test_owner_cannot_access_tenant_invoice_area(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get(route('tenant.invoices.index'))
            ->assertForbidden();
    }

    private function invoiceFor(User $tenant, array $attributes = []): Invoice
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $booking = Booking::factory()->for($room)->create(['tenant_id' => $tenant->id]);

        return Invoice::factory()->for($booking)->create($attributes);
    }
}

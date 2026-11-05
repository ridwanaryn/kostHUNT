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

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_only_sees_invoices_for_their_own_rooms(): void
    {
        $owner = User::factory()->owner()->create();
        $otherOwner = User::factory()->owner()->create();

        $own = $this->invoiceForOwner($owner, ['invoice_number' => 'INV-OWN']);
        $other = $this->invoiceForOwner($otherOwner, ['invoice_number' => 'INV-OTHER']);

        $this->actingAs($owner)
            ->get(route('owner.invoices.index'))
            ->assertOk()
            ->assertSee($own->invoice_number)
            ->assertDontSee($other->invoice_number);
    }

    public function test_owner_can_filter_invoices_by_status(): void
    {
        $owner = User::factory()->owner()->create();
        $paid = $this->invoiceForOwner($owner, [
            'invoice_number' => 'INV-PAID',
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
        ]);
        $unpaid = $this->invoiceForOwner($owner, [
            'invoice_number' => 'INV-UNPAID',
            'status' => InvoiceStatus::Unpaid,
        ]);

        $this->actingAs($owner)
            ->get(route('owner.invoices.index', ['status' => InvoiceStatus::Paid->value]))
            ->assertOk()
            ->assertSee($paid->invoice_number)
            ->assertDontSee($unpaid->invoice_number);
    }

    public function test_owner_can_mark_an_invoice_as_paid(): void
    {
        $owner = User::factory()->owner()->create();
        $invoice = $this->invoiceForOwner($owner, ['status' => InvoiceStatus::Unpaid]);

        $this->actingAs($owner)
            ->post(route('owner.invoices.mark-paid', $invoice))
            ->assertRedirect();

        $invoice->refresh();
        $this->assertSame(InvoiceStatus::Paid, $invoice->status);
        $this->assertNotNull($invoice->paid_at);
    }

    public function test_owner_cannot_mark_another_owners_invoice_as_paid(): void
    {
        $owner = User::factory()->owner()->create();
        $otherOwner = User::factory()->owner()->create();
        $invoice = $this->invoiceForOwner($otherOwner, ['status' => InvoiceStatus::Unpaid]);

        $this->actingAs($owner)
            ->post(route('owner.invoices.mark-paid', $invoice))
            ->assertForbidden();

        $this->assertSame(InvoiceStatus::Unpaid, $invoice->fresh()->status);
    }

    public function test_tenant_cannot_mark_invoices_as_paid(): void
    {
        $tenant = User::factory()->tenant()->create();
        $invoice = $this->invoiceForOwner(User::factory()->owner()->create(), ['status' => InvoiceStatus::Unpaid]);

        $this->actingAs($tenant)
            ->post(route('owner.invoices.mark-paid', $invoice))
            ->assertForbidden();
    }

    private function invoiceForOwner(User $owner, array $attributes = []): Invoice
    {
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $booking = Booking::factory()->for($room)->create();

        return Invoice::factory()->for($booking)->create($attributes);
    }
}

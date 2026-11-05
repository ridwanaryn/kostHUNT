<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Enums\InvoiceStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use App\Notifications\InvoiceDueSoon;
use App\Notifications\InvoiceOverdue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendBillingRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_a_due_soon_reminder(): void
    {
        Notification::fake();
        [$tenant, $invoice] = $this->invoiceDueIn(2);

        $this->artisan('kosthunt:send-billing-reminders')->assertSuccessful();

        Notification::assertSentTo($tenant, InvoiceDueSoon::class);
        $this->assertNotNull($invoice->fresh()->reminder_sent_at);
    }

    public function test_it_sends_an_overdue_reminder(): void
    {
        Notification::fake();
        [$tenant, $invoice] = $this->invoiceDueIn(-5);

        $this->artisan('kosthunt:send-billing-reminders')->assertSuccessful();

        Notification::assertSentTo($tenant, InvoiceOverdue::class);
        $this->assertNotNull($invoice->fresh()->overdue_notified_at);
    }

    public function test_it_does_not_remind_paid_invoices(): void
    {
        Notification::fake();
        $this->invoiceDueIn(-5, InvoiceStatus::Paid);

        $this->artisan('kosthunt:send-billing-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_it_does_not_remind_invoices_far_in_the_future(): void
    {
        Notification::fake();
        $this->invoiceDueIn(10);

        $this->artisan('kosthunt:send-billing-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_it_does_not_send_duplicate_reminders(): void
    {
        Notification::fake();
        [$tenant] = $this->invoiceDueIn(2);

        $this->artisan('kosthunt:send-billing-reminders')->assertSuccessful();
        $this->artisan('kosthunt:send-billing-reminders')->assertSuccessful();

        Notification::assertSentToTimes($tenant, InvoiceDueSoon::class, 1);
    }

    /**
     * @return array{0: User, 1: Invoice}
     */
    private function invoiceDueIn(int $days, InvoiceStatus $status = InvoiceStatus::Unpaid): array
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $tenant = User::factory()->tenant()->create();
        $booking = Booking::factory()->for($room)->create(['tenant_id' => $tenant->id]);

        $invoice = Invoice::factory()->for($booking)->create([
            'due_date' => now()->addDays($days)->toDateString(),
            'status' => $status,
            'paid_at' => $status === InvoiceStatus::Paid ? now() : null,
        ]);

        return [$tenant, $invoice];
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use App\Notifications\BookingCreated;
use App\Notifications\InvoiceDueSoon;
use App\Notifications\InvoicePaid;
use App\Notifications\NewChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_and_mark_notifications_as_read(): void
    {
        $tenant = User::factory()->tenant()->create();
        $invoice = Invoice::factory()->create();
        $tenant->notify(new InvoiceDueSoon($invoice));

        $notification = $tenant->notifications()->firstOrFail();

        $this->actingAs($tenant)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('due soon');

        $this->actingAs($tenant)
            ->post(route('notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);

        $tenant->notify(new InvoiceDueSoon($invoice));

        $this->actingAs($tenant)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $tenant->unreadNotifications()->count());
    }

    public function test_creating_a_booking_notifies_the_owner_and_tenant(): void
    {
        Notification::fake();

        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['is_active' => true]);
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)->post(route('tenant.bookings.store', $room), [
            'start_date' => now()->addDays(5)->toDateString(),
            'period_type' => 'monthly',
            'duration' => 1,
        ]);

        Notification::assertSentTo($owner, BookingCreated::class);
        Notification::assertSentTo($tenant, BookingCreated::class);
    }

    public function test_marking_an_invoice_paid_notifies_the_tenant(): void
    {
        Notification::fake();

        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $tenant = User::factory()->tenant()->create();
        $booking = Booking::factory()->for($room)->create(['tenant_id' => $tenant->id]);
        $invoice = Invoice::factory()->for($booking)->create(['status' => InvoiceStatus::Unpaid]);

        $this->actingAs($owner)->post(route('owner.invoices.mark-paid', $invoice));

        Notification::assertSentTo($tenant, InvoicePaid::class);
    }

    public function test_sending_a_chat_message_notifies_the_recipient(): void
    {
        Notification::fake();

        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();
        $tenant = User::factory()->tenant()->create();
        $conversation = Conversation::factory()->create([
            'tenant_id' => $tenant->id,
            'owner_id' => $owner->id,
            'room_id' => $room->id,
        ]);

        $this->actingAs($tenant)->post(route('chat.messages.store', $conversation), [
            'body' => 'Hello there',
        ]);

        Notification::assertSentTo($owner, NewChatMessage::class);
    }
}

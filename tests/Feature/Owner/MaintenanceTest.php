<?php

declare(strict_types=1);

namespace Tests\Feature\Owner;

use App\Enums\BookingStatus;
use App\Enums\MaintenanceStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\User;
use App\Notifications\MaintenanceStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_only_sees_requests_for_their_own_rooms(): void
    {
        $owner = User::factory()->owner()->create();
        $otherOwner = User::factory()->owner()->create();

        $this->requestFor($owner, ['title' => 'Own Request']);
        $this->requestFor($otherOwner, ['title' => 'Other Request']);

        $this->actingAs($owner)
            ->get(route('owner.maintenance-requests.index'))
            ->assertOk()
            ->assertSee('Own Request')
            ->assertDontSee('Other Request');
    }

    public function test_owner_can_filter_requests_by_status(): void
    {
        $owner = User::factory()->owner()->create();
        $this->requestFor($owner, ['title' => 'Open Request', 'status' => MaintenanceStatus::Open]);
        $this->requestFor($owner, ['title' => 'Working Request', 'status' => MaintenanceStatus::InProgress]);

        $this->actingAs($owner)
            ->get(route('owner.maintenance-requests.index', ['status' => MaintenanceStatus::InProgress->value]))
            ->assertOk()
            ->assertSee('Working Request')
            ->assertDontSee('Open Request');
    }

    public function test_owner_can_update_a_request_status_and_note(): void
    {
        $owner = User::factory()->owner()->create();
        $maintenanceRequest = $this->requestFor($owner, ['status' => MaintenanceStatus::Open]);

        $this->actingAs($owner)->patch(route('owner.maintenance-requests.update', $maintenanceRequest), [
            'status' => MaintenanceStatus::InProgress->value,
            'owner_note' => 'Technician scheduled.',
        ])->assertRedirect();

        $maintenanceRequest->refresh();
        $this->assertSame(MaintenanceStatus::InProgress, $maintenanceRequest->status);
        $this->assertSame('Technician scheduled.', $maintenanceRequest->owner_note);
        $this->assertNull($maintenanceRequest->resolved_at);
    }

    public function test_resolving_a_request_sets_the_resolved_timestamp(): void
    {
        $owner = User::factory()->owner()->create();
        $maintenanceRequest = $this->requestFor($owner, ['status' => MaintenanceStatus::InProgress]);

        $this->actingAs($owner)->patch(route('owner.maintenance-requests.update', $maintenanceRequest), [
            'status' => MaintenanceStatus::Resolved->value,
            'owner_note' => 'Fixed.',
        ])->assertRedirect();

        $maintenanceRequest->refresh();
        $this->assertSame(MaintenanceStatus::Resolved, $maintenanceRequest->status);
        $this->assertNotNull($maintenanceRequest->resolved_at);
    }

    public function test_owner_update_notifies_the_tenant(): void
    {
        Notification::fake();

        $owner = User::factory()->owner()->create();
        $maintenanceRequest = $this->requestFor($owner, ['status' => MaintenanceStatus::Open]);

        $this->actingAs($owner)->patch(route('owner.maintenance-requests.update', $maintenanceRequest), [
            'status' => MaintenanceStatus::Resolved->value,
        ])->assertRedirect();

        Notification::assertSentTo(
            $maintenanceRequest->tenant,
            MaintenanceStatusChanged::class,
        );
    }

    public function test_other_owner_cannot_update_a_request(): void
    {
        $owner = User::factory()->owner()->create();
        $otherOwner = User::factory()->owner()->create();
        $maintenanceRequest = $this->requestFor($owner);

        $this->actingAs($otherOwner)->patch(route('owner.maintenance-requests.update', $maintenanceRequest), [
            'status' => MaintenanceStatus::Resolved->value,
        ])->assertForbidden();
    }

    public function test_tenant_cannot_update_a_request(): void
    {
        $owner = User::factory()->owner()->create();
        $maintenanceRequest = $this->requestFor($owner);
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)->patch(route('owner.maintenance-requests.update', $maintenanceRequest), [
            'status' => MaintenanceStatus::Resolved->value,
        ])->assertForbidden();
    }

    private function requestFor(User $owner, array $attributes = []): MaintenanceRequest
    {
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['is_active' => true]);
        $tenant = User::factory()->tenant()->create();
        $booking = Booking::factory()->for($room)->create([
            'tenant_id' => $tenant->id,
            'status' => BookingStatus::Active,
        ]);

        return MaintenanceRequest::factory()->create(array_merge([
            'booking_id' => $booking->id,
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'status' => MaintenanceStatus::Open,
        ], $attributes));
    }
}

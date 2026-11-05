<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Enums\BookingStatus;
use App\Enums\MaintenanceStatus;
use App\Models\Booking;
use App\Models\Building;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_with_an_active_booking_can_submit_a_request(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->activeBooking($tenant);

        $response = $this->actingAs($tenant)->post(route('tenant.maintenance-requests.store'), [
            'booking_id' => $booking->id,
            'title' => 'Leaking tap',
            'description' => 'The bathroom tap keeps dripping.',
        ]);

        $maintenanceRequest = MaintenanceRequest::query()->firstOrFail();

        $response->assertRedirect(route('tenant.maintenance-requests.show', $maintenanceRequest));
        $this->assertSame($booking->room_id, $maintenanceRequest->room_id);
        $this->assertSame($tenant->id, $maintenanceRequest->tenant_id);
        $this->assertSame(MaintenanceStatus::Open, $maintenanceRequest->status);
    }

    public function test_tenant_cannot_submit_a_request_for_another_tenants_booking(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherTenant = User::factory()->tenant()->create();
        $booking = $this->activeBooking($otherTenant);

        $this->actingAs($tenant)->post(route('tenant.maintenance-requests.store'), [
            'booking_id' => $booking->id,
            'title' => 'Leaking tap',
            'description' => 'The tap is leaking.',
        ])->assertSessionHasErrors('booking_id');
    }

    public function test_tenant_cannot_submit_a_request_for_a_non_active_booking(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->activeBooking($tenant);
        $booking->update(['status' => BookingStatus::Completed]);

        $this->actingAs($tenant)->post(route('tenant.maintenance-requests.store'), [
            'booking_id' => $booking->id,
            'title' => 'Leaking tap',
            'description' => 'The tap is leaking.',
        ])->assertSessionHasErrors('booking_id');
    }

    public function test_request_requires_a_title_and_description(): void
    {
        $tenant = User::factory()->tenant()->create();
        $booking = $this->activeBooking($tenant);

        $this->actingAs($tenant)->post(route('tenant.maintenance-requests.store'), [
            'booking_id' => $booking->id,
        ])->assertSessionHasErrors(['title', 'description']);
    }

    public function test_tenant_can_view_their_own_request(): void
    {
        $tenant = User::factory()->tenant()->create();
        $maintenanceRequest = $this->requestFor($tenant);

        $this->actingAs($tenant)
            ->get(route('tenant.maintenance-requests.show', $maintenanceRequest))
            ->assertOk()
            ->assertSee($maintenanceRequest->title);
    }

    public function test_tenant_cannot_view_another_tenants_request(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherTenant = User::factory()->tenant()->create();
        $maintenanceRequest = $this->requestFor($otherTenant);

        $this->actingAs($tenant)
            ->get(route('tenant.maintenance-requests.show', $maintenanceRequest))
            ->assertForbidden();
    }

    public function test_owner_cannot_access_tenant_maintenance_area(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get(route('tenant.maintenance-requests.index'))
            ->assertForbidden();
    }

    private function activeBooking(User $tenant): Booking
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['is_active' => true]);

        return Booking::factory()->for($room)->create([
            'tenant_id' => $tenant->id,
            'status' => BookingStatus::Active,
        ]);
    }

    private function requestFor(User $tenant): MaintenanceRequest
    {
        $booking = $this->activeBooking($tenant);

        return MaintenanceRequest::factory()->create([
            'booking_id' => $booking->id,
            'room_id' => $booking->room_id,
            'tenant_id' => $tenant->id,
        ]);
    }
}

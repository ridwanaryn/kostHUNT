<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Enums\BookingStatus;
use App\Enums\MaintenanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreMaintenanceRequest;
use App\Models\Booking;
use App\Models\MaintenanceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $requests = MaintenanceRequest::query()
            ->where('tenant_id', $request->user()?->id)
            ->with(['room.building'])
            ->latest()
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('tenant.maintenance-requests.index', ['requests' => $requests]);
    }

    public function create(Request $request): View
    {
        $bookings = Booking::query()
            ->where('tenant_id', $request->user()?->id)
            ->where('status', BookingStatus::Active)
            ->with(['room.building'])
            ->get();

        return view('tenant.maintenance-requests.create', ['bookings' => $bookings]);
    }

    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $booking = Booking::query()->findOrFail((int) $request->validated('booking_id'));

        $maintenanceRequest = MaintenanceRequest::query()->create([
            'booking_id' => $booking->id,
            'room_id' => $booking->room_id,
            'tenant_id' => $user->id,
            'title' => (string) $request->validated('title'),
            'description' => (string) $request->validated('description'),
            'status' => MaintenanceStatus::Open,
        ]);

        return redirect()
            ->route('tenant.maintenance-requests.show', $maintenanceRequest)
            ->with('status', 'Maintenance request submitted.');
    }

    public function show(MaintenanceRequest $maintenanceRequest): View
    {
        $maintenanceRequest->loadMissing(['room.building', 'booking']);
        $this->authorize('view', $maintenanceRequest);

        return view('tenant.maintenance-requests.show', ['maintenanceRequest' => $maintenanceRequest]);
    }
}

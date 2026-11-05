<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Enums\MaintenanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\UpdateMaintenanceRequest;
use App\Models\Building;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Notifications\MaintenanceStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = $request->user()?->id;
        $filters = $request->only(['building_id', 'room_id', 'status']);

        $requests = MaintenanceRequest::query()
            ->whereHas('room.building', fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['tenant', 'room.building'])
            ->when(
                $filters['building_id'] ?? null,
                fn ($query, $buildingId) => $query->whereHas(
                    'room',
                    fn ($roomQuery) => $roomQuery->where('building_id', $buildingId),
                ),
            )
            ->when(
                $filters['room_id'] ?? null,
                fn ($query, $roomId) => $query->where('room_id', $roomId),
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status),
            )
            ->latest()
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('owner.maintenance-requests.index', [
            'requests' => $requests,
            'buildings' => Building::query()->where('owner_id', $ownerId)->orderBy('name')->get(),
            'rooms' => Room::query()
                ->whereHas('building', fn ($query) => $query->where('owner_id', $ownerId))
                ->orderBy('name')
                ->get(),
            'filters' => $filters,
        ]);
    }

    public function update(UpdateMaintenanceRequest $request, MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        $maintenanceRequest->loadMissing(['room.building', 'tenant']);
        $this->authorize('update', $maintenanceRequest);

        $status = MaintenanceStatus::from((string) $request->validated('status'));
        $note = $request->filled('owner_note') ? $request->string('owner_note')->toString() : null;

        $statusChanged = $maintenanceRequest->status !== $status;

        $maintenanceRequest->update([
            'status' => $status,
            'owner_note' => $note,
            'resolved_at' => $status === MaintenanceStatus::Resolved
                ? ($maintenanceRequest->resolved_at ?? now())
                : null,
        ]);

        if ($statusChanged) {
            $maintenanceRequest->tenant->notify(new MaintenanceStatusChanged($maintenanceRequest));
        }

        return back()->with('status', 'Maintenance request updated.');
    }
}

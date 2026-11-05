<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = $request->user()?->id;
        $filters = $request->only(['building_id', 'room_id', 'status']);

        $bookings = Booking::query()
            ->whereHas('room.building', fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['room.building', 'tenant'])
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

        return view('owner.bookings.index', [
            'bookings' => $bookings,
            'buildings' => Building::query()->where('owner_id', $ownerId)->orderBy('name')->get(),
            'rooms' => Room::query()
                ->whereHas('building', fn ($query) => $query->where('owner_id', $ownerId))
                ->orderBy('name')
                ->get(),
            'filters' => $filters,
        ]);
    }

    public function show(Booking $booking): View
    {
        $booking->load(['room.building.photos', 'room.photos', 'invoices', 'tenant']);
        $this->authorize('view', $booking);

        return view('owner.bookings.show', ['booking' => $booking]);
    }
}

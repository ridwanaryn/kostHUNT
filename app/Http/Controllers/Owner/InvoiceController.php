<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = $request->user()?->id;
        $filters = $request->only(['building_id', 'room_id', 'tenant_id', 'status']);

        $invoices = Invoice::query()
            ->whereHas('booking.room.building', fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['booking.tenant', 'booking.room.building'])
            ->when(
                $filters['building_id'] ?? null,
                fn ($query, $buildingId) => $query->whereHas(
                    'booking.room',
                    fn ($roomQuery) => $roomQuery->where('building_id', $buildingId),
                ),
            )
            ->when(
                $filters['room_id'] ?? null,
                fn ($query, $roomId) => $query->whereHas(
                    'booking',
                    fn ($bookingQuery) => $bookingQuery->where('room_id', $roomId),
                ),
            )
            ->when(
                $filters['tenant_id'] ?? null,
                fn ($query, $tenantId) => $query->whereHas(
                    'booking',
                    fn ($bookingQuery) => $bookingQuery->where('tenant_id', $tenantId),
                ),
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status),
            )
            ->latest()
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('owner.invoices.index', [
            'invoices' => $invoices,
            'buildings' => Building::query()->where('owner_id', $ownerId)->orderBy('name')->get(),
            'rooms' => Room::query()
                ->whereHas('building', fn ($query) => $query->where('owner_id', $ownerId))
                ->orderBy('name')
                ->get(),
            'tenants' => User::query()
                ->whereHas('bookings.room.building', fn ($query) => $query->where('owner_id', $ownerId))
                ->orderBy('name')
                ->get(),
            'filters' => $filters,
        ]);
    }
}

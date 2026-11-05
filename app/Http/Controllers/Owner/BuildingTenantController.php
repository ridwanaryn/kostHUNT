<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Building;
use Illuminate\View\View;

class BuildingTenantController extends Controller
{
    public function show(Building $building): View
    {
        $this->authorize('view', $building);

        $bookings = Booking::query()
            ->whereHas('room', fn ($query) => $query->where('building_id', $building->id))
            ->with(['tenant', 'room'])
            ->latest('start_date')
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('owner.tenants.index', [
            'building' => $building,
            'bookings' => $bookings,
        ]);
    }
}

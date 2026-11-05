<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreBuildingRequest;
use App\Http\Requests\Owner\UpdateBuildingRequest;
use App\Models\Building;
use App\Services\PhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuildingController extends Controller
{
    public function __construct(private readonly PhotoService $photos) {}

    public function index(Request $request): View
    {
        $buildings = Building::query()
            ->where('owner_id', $request->user()?->id)
            ->with('photos')
            ->withCount([
                'rooms',
                'rooms as available_rooms_count' => fn ($query) => $query->where('is_active', true),
            ])
            ->latest()
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('owner.buildings.index', ['buildings' => $buildings]);
    }

    public function create(): View
    {
        return view('owner.buildings.create');
    }

    public function store(StoreBuildingRequest $request): RedirectResponse
    {
        $building = new Building($request->validated());
        $building->owner()->associate($request->user());
        $building->save();

        $files = $request->file('photos');

        if (is_array($files)) {
            $this->photos->storeMany($building, $files, true);
        }

        return redirect()
            ->route('owner.buildings.show', $building)
            ->with('status', 'Building created.');
    }

    public function show(Building $building): View
    {
        $this->authorize('view', $building);

        $building->load([
            'photos',
            'rooms' => fn ($query) => $query->with(['photos', 'facilities', 'currentBookings'])->latest(),
        ]);

        return view('owner.buildings.show', ['building' => $building]);
    }

    public function edit(Building $building): View
    {
        $this->authorize('update', $building);

        $building->load('photos');

        return view('owner.buildings.edit', ['building' => $building]);
    }

    public function update(UpdateBuildingRequest $request, Building $building): RedirectResponse
    {
        $this->authorize('update', $building);

        $building->update($request->validated());

        return redirect()
            ->route('owner.buildings.show', $building)
            ->with('status', 'Building updated.');
    }

    public function destroy(Building $building): RedirectResponse
    {
        $this->authorize('delete', $building);

        if ($building->hasActiveBookings()) {
            return back()->withErrors([
                'delete' => 'This building has active or upcoming bookings and cannot be deleted.',
            ]);
        }

        $building->delete();

        return redirect()
            ->route('owner.buildings.index')
            ->with('status', 'Building deleted.');
    }
}

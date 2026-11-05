<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreRoomRequest;
use App\Http\Requests\Owner\UpdateRoomRequest;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Room;
use App\Services\PhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __construct(private readonly PhotoService $photos) {}

    public function create(Building $building): View
    {
        $this->authorize('update', $building);

        return view('owner.rooms.create', [
            'building' => $building,
            'facilities' => Facility::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreRoomRequest $request, Building $building): RedirectResponse
    {
        $this->authorize('update', $building);

        $validated = $request->validated();
        $facilities = is_array($validated['facilities'] ?? null) ? $validated['facilities'] : [];
        unset($validated['facilities'], $validated['photos']);

        $room = new Room($validated);
        $room->building_id = $building->id;
        $room->save();
        $room->facilities()->sync($facilities);

        $files = $request->file('photos');

        if (is_array($files)) {
            $this->photos->storeMany($room, $files, true);
        }

        return redirect()
            ->route('owner.rooms.show', $room)
            ->with('status', 'Room created.');
    }

    public function show(Room $room): View
    {
        $room->loadMissing('building');
        $this->authorize('view', $room);

        $room->load(['facilities', 'photos', 'currentBookings']);

        return view('owner.rooms.show', ['room' => $room]);
    }

    public function edit(Room $room): View
    {
        $room->loadMissing('building');
        $this->authorize('update', $room);

        $room->load(['facilities', 'photos']);

        return view('owner.rooms.edit', [
            'room' => $room,
            'facilities' => Facility::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $room->loadMissing('building');
        $this->authorize('update', $room);

        $validated = $request->validated();
        $facilities = is_array($validated['facilities'] ?? null) ? $validated['facilities'] : [];
        unset($validated['facilities']);

        $room->update($validated);
        $room->facilities()->sync($facilities);

        return redirect()
            ->route('owner.rooms.show', $room)
            ->with('status', 'Room updated.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->loadMissing('building');
        $this->authorize('delete', $room);

        if ($room->hasActiveBookings()) {
            return back()->withErrors([
                'delete' => 'This room has active or upcoming bookings and cannot be deleted.',
            ]);
        }

        $building = $room->building;
        $room->delete();

        return redirect()
            ->route('owner.buildings.show', $building)
            ->with('status', 'Room deleted.');
    }

    public function toggleActive(Room $room): RedirectResponse
    {
        $room->loadMissing('building');
        $this->authorize('update', $room);

        $room->update(['is_active' => ! $room->is_active]);

        return back()->with('status', 'Room visibility updated.');
    }
}

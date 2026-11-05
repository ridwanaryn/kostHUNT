<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\RoomSearchRequest;
use App\Models\Facility;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(RoomSearchRequest $request): View
    {
        $filters = $request->validated();
        $sort = is_string($filters['sort'] ?? null) ? $filters['sort'] : null;

        $rooms = Room::query()
            ->active()
            ->filter($filters)
            ->sort($sort)
            ->with(['building', 'photos', 'facilities', 'currentBookings'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->paginate((int) config('kosthunt.pagination.per_page'))
            ->withQueryString();

        return view('catalog.rooms.index', [
            'rooms' => $rooms,
            'facilities' => Facility::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, Room $room): View
    {
        abort_unless($room->is_active, 404);

        $room->load(['building.photos', 'facilities', 'photos', 'currentBookings', 'reviews.tenant']);

        $isFavorited = false;
        $user = $request->user();

        if ($user !== null) {
            $isFavorited = $user->favoriteRooms()->where('rooms.id', $room->id)->exists();
        }

        return view('catalog.rooms.show', [
            'room' => $room,
            'isFavorited' => $isFavorited,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $rooms = $user->favoriteRooms()
            ->with(['building', 'photos', 'facilities', 'currentBookings'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByDesc('favorites.created_at')
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('tenant.favorites.index', ['rooms' => $rooms]);
    }

    public function store(Request $request, Room $room): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $user->favoriteRooms()->syncWithoutDetaching([$room->id]);

        return back()->with('status', 'Added to favorites.');
    }

    public function destroy(Request $request, Room $room): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $user->favoriteRooms()->detach($room->id);

        return back()->with('status', 'Removed from favorites.');
    }
}

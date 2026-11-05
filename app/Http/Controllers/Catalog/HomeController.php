<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Room;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $rooms = Room::query()
            ->active()
            ->with(['building', 'photos', 'facilities', 'currentBookings'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->latest()
            ->take(5)
            ->get();

        $facilities = Facility::query()->orderBy('name')->take(8)->get();

        $cities = Building::query()
            ->select('city')
            ->selectRaw('count(*) as buildings_count')
            ->groupBy('city')
            ->orderByDesc('buildings_count')
            ->take(4)
            ->get();

        $availableRooms = Room::query()->active()->count();

        return view('catalog.home', [
            'rooms' => $rooms,
            'facilities' => $facilities,
            'cities' => $cities,
            'availableRooms' => $availableRooms,
        ]);
    }
}

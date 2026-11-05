<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\View\View;

class BuildingController extends Controller
{
    public function show(Building $building): View
    {
        $building->load([
            'photos',
            'rooms' => fn ($query) => $query->active()
                ->with(['building', 'photos', 'facilities', 'currentBookings'])
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->latest(),
        ]);

        $building->loadAvg('reviews', 'rating')->loadCount('reviews');

        $reviews = $building->reviews()
            ->with(['tenant', 'room'])
            ->latest()
            ->take(10)
            ->get();

        return view('catalog.buildings.show', [
            'building' => $building,
            'reviews' => $reviews,
        ]);
    }
}

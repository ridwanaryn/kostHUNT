<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreReviewRequest;
use App\Models\Review;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Room $room): RedirectResponse
    {
        $this->authorize('create', [Review::class, $room]);

        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $booking = $user->bookings()
            ->where('room_id', $room->id)
            ->whereIn('status', [BookingStatus::Active, BookingStatus::Completed])
            ->whereDoesntHave('review')
            ->latest('start_date')
            ->first();

        if ($booking === null) {
            return back()->withErrors(['review' => 'You have no eligible booking to review.']);
        }

        $validated = $request->validated();

        Review::query()->create([
            'booking_id' => $booking->id,
            'room_id' => $room->id,
            'tenant_id' => $user->id,
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('status', 'Review submitted. Thank you!');
    }
}

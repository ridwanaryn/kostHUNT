<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Exceptions\RoomNotAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RenewBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;

class BookingRenewalController extends Controller
{
    public function __invoke(
        RenewBookingRequest $request,
        Booking $booking,
        BookingService $bookings,
    ): RedirectResponse {
        $booking->loadMissing('room');
        $this->authorize('renew', $booking);

        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        try {
            $renewed = $bookings->renew($booking, $user, $request->validated());
        } catch (RoomNotAvailableException $exception) {
            return back()->withErrors(['renew' => $exception->getMessage()]);
        }

        return redirect()
            ->route('tenant.bookings.show', $renewed)
            ->with('status', 'Booking renewed.');
    }
}

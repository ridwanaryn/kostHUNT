<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Exceptions\RoomNotAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookings) {}

    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->where('tenant_id', $request->user()?->id)
            ->with(['room.building', 'invoices'])
            ->latest()
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('tenant.bookings.index', ['bookings' => $bookings]);
    }

    public function show(Booking $booking): View
    {
        $booking->load(['room.building.photos', 'room.photos', 'invoices']);
        $this->authorize('view', $booking);

        return view('tenant.bookings.show', ['booking' => $booking]);
    }

    public function store(StoreBookingRequest $request, Room $room): RedirectResponse
    {
        $this->authorize('create', Booking::class);

        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        try {
            $booking = $this->bookings->create($user, $room, $request->validated());
        } catch (RoomNotAvailableException $exception) {
            return back()
                ->withInput()
                ->withErrors(['booking' => $exception->getMessage()]);
        }

        return redirect()
            ->route('tenant.bookings.show', $booking)
            ->with('status', 'Booking confirmed!');
    }
}

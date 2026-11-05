<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RequestTerminationRequest;
use App\Models\Booking;
use App\Services\TerminationService;
use Illuminate\Http\RedirectResponse;

class BookingTerminationController extends Controller
{
    public function __invoke(
        RequestTerminationRequest $request,
        Booking $booking,
        TerminationService $terminations,
    ): RedirectResponse {
        $this->authorize('requestTermination', $booking);

        $terminations->request($booking, $request->validated());

        return back()->with('status', 'Termination request submitted.');
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\DecideTerminationRequest;
use App\Models\Booking;
use App\Services\TerminationService;
use Illuminate\Http\RedirectResponse;

class TerminationDecisionController extends Controller
{
    public function approve(
        DecideTerminationRequest $request,
        Booking $booking,
        TerminationService $terminations,
    ): RedirectResponse {
        $booking->loadMissing(['room.building']);
        $this->authorize('decideTermination', $booking);

        $note = $request->filled('note') ? $request->string('note')->toString() : null;
        $terminations->approve($booking, $note);

        return back()->with('status', 'Termination approved.');
    }

    public function reject(
        DecideTerminationRequest $request,
        Booking $booking,
        TerminationService $terminations,
    ): RedirectResponse {
        $booking->loadMissing(['room.building']);
        $this->authorize('decideTermination', $booking);

        $note = $request->filled('note') ? $request->string('note')->toString() : null;
        $terminations->reject($booking, $note);

        return back()->with('status', 'Termination rejected.');
    }
}

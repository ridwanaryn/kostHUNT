<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Notifications\TerminationDecided;
use App\Notifications\TerminationRequested;

class TerminationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function request(Booking $booking, array $data): Booking
    {
        $booking->update([
            'status' => BookingStatus::TerminationRequested,
            'termination_requested_at' => now(),
            'requested_move_out_date' => $data['requested_move_out_date'],
            'termination_reason' => $data['reason'] ?? null,
        ]);

        $booking->loadMissing('room.building.owner');
        $booking->room->building->owner->notify(new TerminationRequested($booking));

        return $booking;
    }

    public function approve(Booking $booking, ?string $note = null): Booking
    {
        $moveOut = $booking->requested_move_out_date ?? now()->startOfDay();

        $booking->update([
            'status' => BookingStatus::Terminated,
            'end_date' => $moveOut->toDateString(),
            'ended_at' => now(),
            'termination_owner_note' => $note,
        ]);

        $booking->loadMissing('tenant');
        $booking->tenant->notify(new TerminationDecided($booking));

        return $booking;
    }

    public function reject(Booking $booking, ?string $note = null): Booking
    {
        $previousStatus = $booking->start_date->isFuture()
            ? BookingStatus::Upcoming
            : BookingStatus::Active;

        $booking->update([
            'status' => $previousStatus,
            'termination_requested_at' => null,
            'requested_move_out_date' => null,
            'termination_owner_note' => $note,
        ]);

        $booking->loadMissing('tenant');
        $booking->tenant->notify(new TerminationDecided($booking));

        return $booking;
    }
}

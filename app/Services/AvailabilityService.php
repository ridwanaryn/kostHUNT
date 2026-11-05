<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Carbon\CarbonInterface;

class AvailabilityService
{
    /**
     * Determine whether a room is free for the given inclusive date range.
     * An ongoing or upcoming booking that overlaps the range blocks the room.
     */
    public function isAvailable(
        Room $room,
        CarbonInterface $start,
        CarbonInterface $end,
        ?int $ignoreBookingId = null,
    ): bool {
        if (! $room->is_active) {
            return false;
        }

        return ! Booking::query()
            ->where('room_id', $room->id)
            ->occupying()
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->exists();
    }
}

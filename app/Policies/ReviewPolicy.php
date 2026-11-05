<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Room;
use App\Models\User;

class ReviewPolicy
{
    public function create(User $user, Room $room): bool
    {
        if (! $user->isTenant()) {
            return false;
        }

        return $user->bookings()
            ->where('room_id', $room->id)
            ->whereIn('status', [BookingStatus::Active, BookingStatus::Completed])
            ->whereDoesntHave('review')
            ->exists();
    }
}

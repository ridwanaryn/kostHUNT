<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $booking->tenant_id === $user->id
            || $booking->room->building->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isTenant();
    }

    public function renew(User $user, Booking $booking): bool
    {
        if ($booking->tenant_id !== $user->id || $booking->status !== BookingStatus::Active) {
            return false;
        }

        return now()->startOfDay()->betweenIncluded(
            $booking->end_date->copy()->subDays(30),
            $booking->end_date->copy()->endOfDay(),
        );
    }

    public function requestTermination(User $user, Booking $booking): bool
    {
        return $booking->tenant_id === $user->id
            && in_array($booking->status, [BookingStatus::Upcoming, BookingStatus::Active], true);
    }

    public function decideTermination(User $user, Booking $booking): bool
    {
        return $booking->status === BookingStatus::TerminationRequested
            && $booking->room->building->owner_id === $user->id;
    }
}

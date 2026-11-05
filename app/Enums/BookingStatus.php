<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingStatus: string
{
    case Upcoming = 'upcoming';
    case Active = 'active';
    case TerminationRequested = 'termination_requested';
    case Completed = 'completed';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Upcoming => 'Upcoming',
            self::Active => 'Active',
            self::TerminationRequested => 'Termination requested',
            self::Completed => 'Completed',
            self::Terminated => 'Terminated',
        };
    }

    public function occupiesRoom(): bool
    {
        return in_array($this, [
            self::Upcoming,
            self::Active,
            self::TerminationRequested,
        ], true);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Upcoming => 'bg-yellow text-black',
            self::Active => 'bg-mint text-black',
            self::TerminationRequested => 'bg-coral text-white',
            self::Completed => 'bg-lilac text-black',
            self::Terminated => 'bg-gray-200 text-gray-700',
        };
    }
}

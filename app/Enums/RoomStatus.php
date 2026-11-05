<?php

declare(strict_types=1);

namespace App\Enums;

enum RoomStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Upcoming = 'upcoming';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Occupied => 'Occupied',
            self::Upcoming => 'Upcoming',
            self::Hidden => 'Hidden',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Available => 'bg-mint text-black',
            self::Occupied => 'bg-lilac text-black',
            self::Upcoming => 'bg-yellow text-black',
            self::Hidden => 'bg-gray-200 text-gray-700',
        };
    }
}

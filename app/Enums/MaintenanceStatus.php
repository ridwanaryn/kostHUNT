<?php

declare(strict_types=1);

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In progress',
            self::Resolved => 'Resolved',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-coral text-white',
            self::InProgress => 'bg-yellow text-black',
            self::Resolved => 'bg-mint text-black',
        };
    }
}

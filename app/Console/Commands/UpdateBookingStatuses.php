<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('kosthunt:update-booking-statuses')]
#[Description('Advance booking statuses based on their dates')]
class UpdateBookingStatuses extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->startOfDay()->toDateString();

        $activated = Booking::query()
            ->where('status', BookingStatus::Upcoming)
            ->whereDate('start_date', '<=', $today)
            ->update(['status' => BookingStatus::Active->value]);

        $completed = Booking::query()
            ->whereIn('status', [BookingStatus::Active, BookingStatus::Upcoming])
            ->whereDate('end_date', '<', $today)
            ->update([
                'status' => BookingStatus::Completed->value,
                'ended_at' => now(),
            ]);

        $this->info("Activated {$activated} booking(s), completed {$completed} booking(s).");

        return self::SUCCESS;
    }
}

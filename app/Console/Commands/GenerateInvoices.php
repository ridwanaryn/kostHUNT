<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('kosthunt:generate-invoices')]
#[Description('Generate upcoming invoices for active and upcoming bookings')]
class GenerateInvoices extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(InvoiceService $invoices): int
    {
        $leadDays = (int) config('kosthunt.billing.generate_days_before');

        Booking::query()
            ->whereIn('status', [BookingStatus::Upcoming, BookingStatus::Active])
            ->with('invoices')
            ->chunkById(100, function ($bookings) use ($invoices, $leadDays): void {
                foreach ($bookings as $booking) {
                    $invoices->generateDueForBooking($booking, $leadDays);
                }
            });

        $this->info('Invoice generation complete.');

        return self::SUCCESS;
    }
}

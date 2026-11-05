<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\PeriodType;
use App\Models\Booking;
use App\Models\Invoice;
use App\Notifications\InvoicePaid;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class InvoiceService
{
    public function generateInitial(Booking $booking): Invoice
    {
        return $this->generateForPeriod($booking, Carbon::parse($booking->start_date)->startOfDay());
    }

    /**
     * Create any invoices whose billing period starts within the lead window.
     * Existing periods are skipped, so the command is safe to run daily.
     */
    public function generateDueForBooking(Booking $booking, int $leadDays): void
    {
        $start = Carbon::parse($booking->start_date)->startOfDay();
        $threshold = now()->startOfDay()->addDays($leadDays);
        $existingStarts = $booking->invoices
            ->map(fn (Invoice $invoice): string => $invoice->period_start->toDateString())
            ->all();

        for ($period = 0; $period < $booking->duration; $period++) {
            $periodStart = match ($booking->period_type) {
                PeriodType::Monthly => $start->copy()->addMonthsNoOverflow($period),
                PeriodType::Yearly => $start->copy()->addYearsNoOverflow($period),
            };

            if (in_array($periodStart->toDateString(), $existingStarts, true)) {
                continue;
            }

            if ($periodStart->gt($threshold)) {
                continue;
            }

            $this->generateForPeriod($booking, $periodStart);
        }
    }

    public function generateForPeriod(Booking $booking, CarbonInterface $periodStart): Invoice
    {
        $periodEnd = match ($booking->period_type) {
            PeriodType::Monthly => $periodStart->copy()->addMonthNoOverflow()->subDay(),
            PeriodType::Yearly => $periodStart->copy()->addYearNoOverflow()->subDay(),
        };

        return Invoice::query()->create([
            'booking_id' => $booking->id,
            'invoice_number' => $this->nextNumber(),
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'amount' => $booking->pricePerPeriod(),
            'due_date' => $periodStart->toDateString(),
            'status' => InvoiceStatus::Unpaid,
        ]);
    }

    public function markPaid(Invoice $invoice): void
    {
        $invoice->update([
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
        ]);

        $invoice->loadMissing('booking.tenant');
        $invoice->booking->tenant->notify(new InvoicePaid($invoice));
    }

    private function nextNumber(): string
    {
        return 'INV-'.Str::ulid();
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\InvoiceDueSoon;
use App\Notifications\InvoiceOverdue;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('kosthunt:send-billing-reminders')]
#[Description('Notify tenants about invoices that are due soon or overdue')]
class SendBillingReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $reminderDays = (int) config('kosthunt.billing.reminder_days');
        $today = now()->startOfDay();
        $dueSoonThreshold = $today->copy()->addDays($reminderDays);

        Invoice::query()
            ->unpaid()
            ->with('booking.tenant')
            ->chunkById(100, function ($invoices) use ($today, $dueSoonThreshold): void {
                foreach ($invoices as $invoice) {
                    $tenant = $invoice->booking->tenant;
                    $dueDate = $invoice->due_date->startOfDay();

                    if ($dueDate->lt($today) && $invoice->overdue_notified_at === null) {
                        $tenant->notify(new InvoiceOverdue($invoice));
                        $invoice->update(['overdue_notified_at' => now()]);
                    } elseif ($dueDate->betweenIncluded($today, $dueSoonThreshold) && $invoice->reminder_sent_at === null) {
                        $tenant->notify(new InvoiceDueSoon($invoice));
                        $invoice->update(['reminder_sent_at' => now()]);
                    }
                }
            });

        $this->info('Billing reminders sent.');

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invoice;
use App\Support\Money;
use Illuminate\Notifications\Notification;

class InvoiceDueSoon extends Notification
{
    public function __construct(private readonly Invoice $invoice) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => $this->invoice->amount,
            'due_date' => $this->invoice->due_date->toDateString(),
            'message' => 'Invoice '.$this->invoice->invoice_number.' is due soon: '.Money::format($this->invoice->amount).'.',
            'url' => route('tenant.invoices.show', $this->invoice),
        ];
    }
}

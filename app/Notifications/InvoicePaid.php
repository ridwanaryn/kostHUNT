<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invoice;
use App\Support\Money;
use Illuminate\Notifications\Notification;

class InvoicePaid extends Notification
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
            'message' => 'Payment received for invoice '.$this->invoice->invoice_number
                .' ('.Money::format($this->invoice->amount).').',
            'url' => route('tenant.invoices.show', $this->invoice),
        ];
    }
}

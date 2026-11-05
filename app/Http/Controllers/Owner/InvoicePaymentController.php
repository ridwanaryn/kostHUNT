<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;

class InvoicePaymentController extends Controller
{
    public function __invoke(Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        $invoice->loadMissing(['booking.room.building']);
        $this->authorize('markPaid', $invoice);

        $invoices->markPaid($invoice);

        return back()->with('status', 'Invoice marked as paid.');
    }
}

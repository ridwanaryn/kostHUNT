<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = Invoice::query()
            ->whereHas('booking', fn ($query) => $query->where('tenant_id', $request->user()?->id))
            ->with(['booking.room.building'])
            ->latest()
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('tenant.invoices.index', ['invoices' => $invoices]);
    }

    public function show(Invoice $invoice): View
    {
        $invoice->loadMissing(['booking.room.building']);
        $this->authorize('view', $invoice);

        return view('tenant.invoices.show', ['invoice' => $invoice]);
    }
}

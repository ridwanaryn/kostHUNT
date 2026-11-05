<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        return $invoice->booking->tenant_id === $user->id
            || $invoice->booking->room->building->owner_id === $user->id;
    }

    public function markPaid(User $user, Invoice $invoice): bool
    {
        return $invoice->booking->room->building->owner_id === $user->id;
    }
}

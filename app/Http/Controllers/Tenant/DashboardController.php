<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $bookings = $user->bookings()
            ->with(['room.building'])
            ->latest()
            ->take(5)
            ->get();

        $unpaidInvoices = Invoice::query()
            ->where('status', InvoiceStatus::Unpaid)
            ->whereHas('booking', fn ($query) => $query->where('tenant_id', $user->id))
            ->with(['booking.room'])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        return view('tenant.dashboard', [
            'bookings' => $bookings,
            'unpaidInvoices' => $unpaidInvoices,
            'favoritesCount' => $user->favoriteRooms()->count(),
            'unreadNotifications' => $user->unreadNotifications()->count(),
        ]);
    }
}

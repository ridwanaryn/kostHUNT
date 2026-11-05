<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\InvoiceStatus;
use App\Enums\MaintenanceStatus;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\User;

class OwnerDashboardService
{
    /**
     * @return array<string, int|float>
     */
    public function summary(User $owner): array
    {
        $buildingIds = $owner->buildings()->pluck('id');

        $roomsCount = Room::query()->whereIn('building_id', $buildingIds)->count();
        $occupiedCount = Room::query()
            ->whereIn('building_id', $buildingIds)
            ->whereHas('bookings', fn ($query) => $query->where('status', BookingStatus::Active))
            ->count();

        $incomeThisMonth = (int) Invoice::query()
            ->where('status', InvoiceStatus::Paid)
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->whereHas('booking.room.building', fn ($query) => $query->where('owner_id', $owner->id))
            ->sum('amount');

        $unpaidInvoices = Invoice::query()
            ->where('status', InvoiceStatus::Unpaid)
            ->whereHas('booking.room.building', fn ($query) => $query->where('owner_id', $owner->id))
            ->count();

        $overdueInvoices = Invoice::query()
            ->where('status', InvoiceStatus::Unpaid)
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereHas('booking.room.building', fn ($query) => $query->where('owner_id', $owner->id))
            ->count();

        $pendingTerminations = Booking::query()
            ->where('status', BookingStatus::TerminationRequested)
            ->whereHas('room.building', fn ($query) => $query->where('owner_id', $owner->id))
            ->count();

        $openMaintenance = MaintenanceRequest::query()
            ->where('status', '!=', MaintenanceStatus::Resolved)
            ->whereHas('room.building', fn ($query) => $query->where('owner_id', $owner->id))
            ->count();

        return [
            'buildings_count' => $owner->buildings()->count(),
            'rooms_count' => $roomsCount,
            'occupied_count' => $occupiedCount,
            'occupancy_rate' => $roomsCount > 0 ? (int) round($occupiedCount / $roomsCount * 100) : 0,
            'income_this_month' => $incomeThisMonth,
            'unpaid_invoices' => $unpaidInvoices,
            'overdue_invoices' => $overdueInvoices,
            'pending_terminations' => $pendingTerminations,
            'open_maintenance' => $openMaintenance,
        ];
    }
}

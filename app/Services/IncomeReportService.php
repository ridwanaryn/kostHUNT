<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Carbon;

class IncomeReportService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(User $owner, array $filters): array
    {
        $groupBy = is_string($filters['group_by'] ?? null) && $filters['group_by'] === 'year' ? 'year' : 'month';
        $from = is_string($filters['from'] ?? null) ? Carbon::parse($filters['from'])->startOfDay() : null;
        $to = is_string($filters['to'] ?? null) ? Carbon::parse($filters['to'])->endOfDay() : null;
        $buildingId = $filters['building_id'] ?? null;

        $invoices = Invoice::query()
            ->where('status', InvoiceStatus::Paid)
            ->whereNotNull('paid_at')
            ->whereHas('booking.room.building', fn ($query) => $query->where('owner_id', $owner->id))
            ->when(
                $buildingId,
                fn ($query, $id) => $query->whereHas(
                    'booking.room',
                    fn ($roomQuery) => $roomQuery->where('building_id', $id),
                ),
            )
            ->when($from, fn ($query, $date) => $query->where('paid_at', '>=', $date))
            ->when($to, fn ($query, $date) => $query->where('paid_at', '<=', $date))
            ->with(['booking.room.building'])
            ->get();

        $grouped = $invoices->groupBy(fn (Invoice $invoice): string => $groupBy === 'year'
            ? (string) $invoice->paid_at?->format('Y')
            : (string) $invoice->paid_at?->format('Y-m'));

        $byGroup = $grouped->map(fn ($group, $period): array => [
            'period' => (string) $period,
            'total' => (int) $group->sum('amount'),
            'count' => $group->count(),
        ])->values()->sortBy('period')->values();

        $byBuilding = $invoices
            ->groupBy(fn (Invoice $invoice): string => $invoice->booking->room->building->name)
            ->map(fn ($group, $name): array => [
                'building' => (string) $name,
                'total' => (int) $group->sum('amount'),
            ])
            ->values()
            ->sortByDesc('total')
            ->values();

        return [
            'total' => (int) $invoices->sum('amount'),
            'count' => $invoices->count(),
            'group_by' => $groupBy,
            'by_group' => $byGroup,
            'by_building' => $byBuilding,
        ];
    }
}

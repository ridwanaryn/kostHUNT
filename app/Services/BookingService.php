<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PeriodType;
use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Notifications\BookingCreated;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly InvoiceService $invoices,
    ) {}

    /**
     * Create a booking and its first invoice atomically, locking the room row.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws RoomNotAvailableException
     */
    public function create(User $tenant, Room $room, array $data): Booking
    {
        $start = Carbon::parse((string) $data['start_date'])->startOfDay();
        $periodType = PeriodType::from((string) $data['period_type']);
        $duration = (int) $data['duration'];
        $end = $this->calculateEndDate($start, $periodType, $duration);

        $booking = DB::transaction(function () use ($tenant, $room, $periodType, $duration, $start, $end): Booking {
            $lockedRoom = Room::query()->whereKey($room->id)->lockForUpdate()->firstOrFail();

            if (! $this->availability->isAvailable($lockedRoom, $start, $end)) {
                throw new RoomNotAvailableException;
            }

            $perPeriod = $this->priceForPeriod($lockedRoom, $periodType);

            $booking = new Booking([
                'room_id' => $lockedRoom->id,
                'period_type' => $periodType,
                'duration' => $duration,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'rent_amount' => $perPeriod * $duration,
                'status' => $start->isFuture() ? BookingStatus::Upcoming : BookingStatus::Active,
            ]);
            $booking->tenant_id = $tenant->id;
            $booking->save();

            $this->invoices->generateInitial($booking);

            return $booking;
        });

        $booking->loadMissing(['room.building.owner']);
        $booking->room->building->owner->notify(new BookingCreated($booking, forOwner: true));
        $tenant->notify(new BookingCreated($booking));

        return $booking;
    }

    public function calculateEndDate(CarbonInterface $start, PeriodType $periodType, int $duration): CarbonInterface
    {
        return match ($periodType) {
            PeriodType::Monthly => $start->copy()->addMonthsNoOverflow($duration)->subDay(),
            PeriodType::Yearly => $start->copy()->addYearsNoOverflow($duration)->subDay(),
        };
    }

    /**
     * Extend an active booking by creating a linked booking that starts the day after it ends.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws RoomNotAvailableException
     */
    public function renew(Booking $booking, User $tenant, array $data): Booking
    {
        $start = $booking->end_date->copy()->addDay()->startOfDay();
        $data['start_date'] = $start->toDateString();

        $renewed = $this->create($tenant, $booking->room, $data);
        $renewed->update(['renewed_from_id' => $booking->id]);

        return $renewed;
    }

    /**
     * @throws RoomNotAvailableException
     */
    private function priceForPeriod(Room $room, PeriodType $periodType): int
    {
        if ($periodType === PeriodType::Yearly) {
            if ($room->price_yearly === null) {
                throw new RoomNotAvailableException('Yearly rental is not available for this room.');
            }

            return $room->price_yearly;
        }

        return $room->price_monthly;
    }
}

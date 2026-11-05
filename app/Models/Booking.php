<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PeriodType;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int $room_id
 * @property int|null $renewed_from_id
 * @property PeriodType $period_type
 * @property int $duration
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property int $rent_amount
 * @property BookingStatus $status
 * @property Carbon|null $termination_requested_at
 * @property Carbon|null $requested_move_out_date
 * @property string|null $termination_reason
 * @property string|null $termination_owner_note
 * @property Carbon|null $ended_at
 * @property-read User $tenant
 * @property-read Room $room
 * @property-read Collection<int, Invoice> $invoices
 * @property-read Booking|null $renewedFrom
 * @property-read Collection<int, Booking> $renewals
 * @property-read Review|null $review
 */
#[Fillable([
    'room_id',
    'renewed_from_id',
    'period_type',
    'duration',
    'start_date',
    'end_date',
    'rent_amount',
    'status',
    'termination_requested_at',
    'requested_move_out_date',
    'termination_reason',
    'termination_owner_note',
    'ended_at',
])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_type' => PeriodType::class,
            'status' => BookingStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'termination_requested_at' => 'datetime',
            'requested_move_out_date' => 'date',
            'ended_at' => 'datetime',
            'duration' => 'integer',
            'rent_amount' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return HasMany<Invoice, $this>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function renewedFrom(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'renewed_from_id');
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function renewals(): HasMany
    {
        return $this->hasMany(Booking::class, 'renewed_from_id');
    }

    /**
     * @return HasOne<Review, $this>
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeOccupying(Builder $query): Builder
    {
        return $query->whereIn('status', [
            BookingStatus::Upcoming,
            BookingStatus::Active,
            BookingStatus::TerminationRequested,
        ]);
    }

    public function isActive(): bool
    {
        return $this->status === BookingStatus::Active;
    }

    public function isUpcoming(): bool
    {
        return $this->status === BookingStatus::Upcoming;
    }

    public function pricePerPeriod(): int
    {
        return intdiv($this->rent_amount, max(1, $this->duration));
    }
}

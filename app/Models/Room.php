<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Models\Concerns\HasPhotos;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $building_id
 * @property string $name
 * @property string|null $description
 * @property int|null $size_sqm
 * @property int $price_monthly
 * @property int|null $price_yearly
 * @property bool $electricity_included
 * @property bool $water_included
 * @property bool $is_active
 * @property-read Building $building
 * @property-read Collection<int, Facility> $facilities
 * @property-read Collection<int, Photo> $photos
 * @property-read Collection<int, Booking> $bookings
 * @property-read Collection<int, Booking> $currentBookings
 * @property-read Collection<int, Review> $reviews
 * @property-read Collection<int, User> $favoritedBy
 */
#[Fillable([
    'name',
    'description',
    'size_sqm',
    'price_monthly',
    'price_yearly',
    'electricity_included',
    'water_included',
    'is_active',
])]
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory, HasPhotos, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size_sqm' => 'integer',
            'price_monthly' => 'integer',
            'price_yearly' => 'integer',
            'electricity_included' => 'boolean',
            'water_included' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Building, $this>
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * @return BelongsToMany<Facility, $this>
     */
    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Bookings that currently occupy the room (upcoming, active, or pending termination).
     *
     * @return HasMany<Booking, $this>
     */
    public function currentBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->whereIn('status', [
            BookingStatus::Upcoming,
            BookingStatus::Active,
            BookingStatus::TerminationRequested,
        ]);
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'room_id', 'tenant_id');
    }

    /**
     * @param  Builder<Room>  $query
     * @return Builder<Room>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Room>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Room>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $city = $filters['city'] ?? null;
        if (is_string($city) && $city !== '') {
            $query->whereHas('building', fn ($related) => $related->where('city', 'like', '%'.$city.'%'));
        }

        $province = $filters['province'] ?? null;
        if (is_string($province) && $province !== '') {
            $query->whereHas('building', fn ($related) => $related->where('province', 'like', '%'.$province.'%'));
        }

        $minPrice = $filters['min_price'] ?? null;
        if (is_numeric($minPrice)) {
            $query->where('price_monthly', '>=', (int) $minPrice);
        }

        $maxPrice = $filters['max_price'] ?? null;
        if (is_numeric($maxPrice)) {
            $query->where('price_monthly', '<=', (int) $maxPrice);
        }

        $facilities = $filters['facilities'] ?? null;
        if (is_array($facilities) && $facilities !== []) {
            $query->whereHas('facilities', fn ($related) => $related->whereIn('facilities.id', $facilities));
        }

        $startDate = $filters['start_date'] ?? null;
        if (is_string($startDate) && $startDate !== '') {
            $query->whereDoesntHave('bookings', fn ($related) => $related
                ->whereIn('status', [
                    BookingStatus::Upcoming,
                    BookingStatus::Active,
                    BookingStatus::TerminationRequested,
                ])
                ->whereDate('start_date', '<=', $startDate)
                ->whereDate('end_date', '>=', $startDate));
        }

        return $query;
    }

    /**
     * @param  Builder<Room>  $query
     * @return Builder<Room>
     */
    public function scopeSort(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price_monthly')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price_monthly')->orderBy('id'),
            default => $query->latest()->orderByDesc('id'),
        };
    }

    /**
     * The room's current availability, derived from its active booking state.
     */
    public function status(): RoomStatus
    {
        if (! $this->is_active) {
            return RoomStatus::Hidden;
        }

        $bookings = $this->relationLoaded('currentBookings')
            ? $this->currentBookings
            : $this->currentBookings()->get();

        if ($bookings->contains(fn (Booking $booking): bool => in_array($booking->status, [
            BookingStatus::Active,
            BookingStatus::TerminationRequested,
        ], true))) {
            return RoomStatus::Occupied;
        }

        if ($bookings->isNotEmpty()) {
            return RoomStatus::Upcoming;
        }

        return RoomStatus::Available;
    }

    public function hasActiveBookings(): bool
    {
        return $this->bookings()->occupying()->exists();
    }
}

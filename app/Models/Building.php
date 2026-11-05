<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasPhotos;
use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $owner_id
 * @property string $name
 * @property string|null $description
 * @property string $street
 * @property string $city
 * @property string $province
 * @property string|null $house_rules
 * @property-read User $owner
 * @property-read Collection<int, Room> $rooms
 * @property-read Collection<int, Photo> $photos
 * @property-read Collection<int, Review> $reviews
 */
#[Fillable(['name', 'description', 'street', 'city', 'province', 'house_rules'])]
class Building extends Model
{
    /** @use HasFactory<BuildingFactory> */
    use HasFactory, HasPhotos, SoftDeletes;

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return HasMany<Room, $this>
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * @return HasManyThrough<Review, Room, $this>
     */
    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Room::class);
    }

    /**
     * A building with upcoming or active bookings cannot be deleted.
     */
    public function hasActiveBookings(): bool
    {
        return $this->rooms()
            ->whereHas('bookings', fn ($query) => $query->occupying())
            ->exists();
    }
}

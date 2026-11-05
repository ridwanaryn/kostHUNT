<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaintenanceStatus;
use Database\Factories\MaintenanceRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $booking_id
 * @property int $room_id
 * @property int $tenant_id
 * @property string $title
 * @property string $description
 * @property MaintenanceStatus $status
 * @property string|null $owner_note
 * @property Carbon|null $resolved_at
 * @property-read Booking $booking
 * @property-read Room $room
 * @property-read User $tenant
 */
#[Fillable([
    'booking_id',
    'room_id',
    'tenant_id',
    'title',
    'description',
    'status',
    'owner_note',
    'resolved_at',
])]
class MaintenanceRequest extends Model
{
    /** @use HasFactory<MaintenanceRequestFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MaintenanceStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function isResolved(): bool
    {
        return $this->status === MaintenanceStatus::Resolved;
    }
}

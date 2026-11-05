<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification
{
    public function __construct(
        private readonly Booking $booking,
        private readonly bool $forOwner = false,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'message' => 'Booking for "'.$this->booking->room->name.'" from '
                .$this->booking->start_date->format('d M Y').' to '
                .$this->booking->end_date->format('d M Y').'.',
            'url' => $this->forOwner
                ? route('owner.bookings.show', $this->booking)
                : route('tenant.bookings.show', $this->booking),
        ];
    }
}

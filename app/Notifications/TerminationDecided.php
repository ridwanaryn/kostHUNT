<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class TerminationDecided extends Notification
{
    public function __construct(private readonly Booking $booking) {}

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
            'message' => 'Your termination request for "'.$this->booking->room->name
                .'" was '.$this->booking->status->label().'.',
            'url' => route('tenant.bookings.show', $this->booking),
        ];
    }
}

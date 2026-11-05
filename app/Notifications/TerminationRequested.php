<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class TerminationRequested extends Notification
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
            'message' => 'A tenant requested to end the booking for "'.$this->booking->room->name
                .'" on '.$this->booking->requested_move_out_date?->format('d M Y').'.',
            'url' => route('owner.bookings.show', $this->booking),
        ];
    }
}

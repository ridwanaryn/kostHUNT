<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $booking_id
 * @property string $invoice_number
 * @property Carbon $period_start
 * @property Carbon $period_end
 * @property int $amount
 * @property Carbon $due_date
 * @property InvoiceStatus $status
 * @property Carbon|null $paid_at
 * @property Carbon|null $reminder_sent_at
 * @property Carbon|null $overdue_notified_at
 * @property-read Booking $booking
 */
#[Fillable([
    'booking_id',
    'invoice_number',
    'period_start',
    'period_end',
    'amount',
    'due_date',
    'status',
    'paid_at',
    'reminder_sent_at',
    'overdue_notified_at',
])]
class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'period_start' => 'date',
            'period_end' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'overdue_notified_at' => 'datetime',
            'amount' => 'integer',
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
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Unpaid);
    }

    /**
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->unpaid()->whereDate('due_date', '<', now()->toDateString());
    }

    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::Unpaid
            && $this->due_date->isPast()
            && ! $this->due_date->isToday();
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::Paid;
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now()->startOfDay();

        return [
            'booking_id' => Booking::factory(),
            'invoice_number' => 'INV-'.Str::ulid(),
            'period_start' => $start->toDateString(),
            'period_end' => $start->copy()->addMonthNoOverflow()->subDay()->toDateString(),
            'amount' => 1_500_000,
            'due_date' => $start->toDateString(),
            'status' => InvoiceStatus::Unpaid,
        ];
    }
}

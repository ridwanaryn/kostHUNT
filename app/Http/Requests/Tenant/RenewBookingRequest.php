<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\Enums\PeriodType;
use App\Models\Booking;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RenewBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxDuration = (int) config('kosthunt.booking.max_duration');

        return [
            'period_type' => ['required', Rule::enum(PeriodType::class)],
            'duration' => ['required', 'integer', 'min:1', 'max:'.$maxDuration],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $booking = $this->route('booking');

            if (! $booking instanceof Booking) {
                return;
            }

            $priceYearly = $booking->room()->value('price_yearly');

            if ($this->input('period_type') === PeriodType::Yearly->value && $priceYearly === null) {
                $validator->errors()->add('period_type', 'Yearly rental is not available for this room.');
            }
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\Enums\PeriodType;
use App\Models\Room;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTenant() ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $advanceDays = (int) config('kosthunt.booking.advance_days');
        $maxDuration = (int) config('kosthunt.booking.max_duration');

        return [
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:'.Carbon::now()->addDays($advanceDays)->toDateString(),
            ],
            'period_type' => ['required', Rule::enum(PeriodType::class)],
            'duration' => ['required', 'integer', 'min:1', 'max:'.$maxDuration],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $room = $this->route('room');

            if (! $room instanceof Room) {
                return;
            }

            if (! $room->is_active) {
                $validator->errors()->add('room', 'This room is not available.');
            }

            if ($this->input('period_type') === PeriodType::Yearly->value && $room->price_yearly === null) {
                $validator->errors()->add('period_type', 'Yearly rental is not available for this room.');
            }
        });
    }
}

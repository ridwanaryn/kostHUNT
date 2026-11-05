<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\Models\Booking;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RequestTerminationRequest extends FormRequest
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
        return [
            'requested_move_out_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $booking = $this->route('booking');
            $requested = $this->input('requested_move_out_date');

            if (! $booking instanceof Booking || ! is_string($requested) || $requested === '') {
                return;
            }

            if ($requested >= $booking->end_date->toDateString()) {
                $validator->errors()->add(
                    'requested_move_out_date',
                    'The move-out date must be before the booking ends.',
                );
            }
        });
    }
}

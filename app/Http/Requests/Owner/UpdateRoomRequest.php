<?php

declare(strict_types=1);

namespace App\Http\Requests\Owner;

use App\Models\Room;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
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
        $room = $this->room();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms', 'name')
                    ->where('building_id', $room->building_id)
                    ->ignore($room->id),
            ],
            'description' => ['nullable', 'string'],
            'size_sqm' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'price_monthly' => ['required', 'integer', 'min:0'],
            'price_yearly' => ['nullable', 'integer', 'min:0'],
            'electricity_included' => ['boolean'],
            'water_included' => ['boolean'],
            'is_active' => ['boolean'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['integer', 'exists:facilities,id'],
        ];
    }

    private function room(): Room
    {
        $room = $this->route('room');

        if (! $room instanceof Room) {
            abort(404);
        }

        return $room;
    }
}

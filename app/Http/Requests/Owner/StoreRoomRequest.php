<?php

declare(strict_types=1);

namespace App\Http\Requests\Owner;

use App\Models\Building;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
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
        $maxPhotos = (int) config('kosthunt.uploads.max_per_room');
        $maxSizeKb = (int) config('kosthunt.uploads.max_size_kb');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms', 'name')->where('building_id', $this->building()->id),
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
            'photos' => ['nullable', 'array', 'max:'.$maxPhotos],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.$maxSizeKb],
        ];
    }

    private function building(): Building
    {
        $building = $this->route('building');

        if (! $building instanceof Building) {
            abort(404);
        }

        return $building;
    }
}

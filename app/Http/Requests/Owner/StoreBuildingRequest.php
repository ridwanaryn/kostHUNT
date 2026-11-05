<?php

declare(strict_types=1);

namespace App\Http\Requests\Owner;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingRequest extends FormRequest
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
        $maxPhotos = (int) config('kosthunt.uploads.max_per_building');
        $maxSizeKb = (int) config('kosthunt.uploads.max_size_kb');

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'house_rules' => ['nullable', 'string'],
            'photos' => ['nullable', 'array', 'max:'.$maxPhotos],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.$maxSizeKb],
        ];
    }
}

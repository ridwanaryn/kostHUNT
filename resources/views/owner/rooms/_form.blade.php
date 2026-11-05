{{-- Expects: $room (nullable), $building, $action, $method, $submitLabel, $cancelUrl, $facilities --}}
@php
    $selectedFacilities = old('facilities', $room ? $room->facilities->pluck('id')->all() : []);
    $selectedFacilities = array_map('intval', (array) $selectedFacilities);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <div class="border-2 border-black rounded-xl bg-coral text-white px-4 py-3 font-bold shadow-[3px_3px_0px_#000]">
            Please fix the highlighted fields.
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="name" class="block font-bold text-sm uppercase tracking-wide mb-1">Room name / number</label>
            <input id="name" name="name" type="text" value="{{ old('name', $room?->name) }}"
                   class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan" required>
            @error('name') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="size_sqm" class="block font-bold text-sm uppercase tracking-wide mb-1">Size (m&sup2;)</label>
            <input id="size_sqm" name="size_sqm" type="number" min="1" value="{{ old('size_sqm', $room?->size_sqm) }}"
                   class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
            @error('size_sqm') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="description" class="block font-bold text-sm uppercase tracking-wide mb-1">Description</label>
        <textarea id="description" name="description" rows="3"
                  class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">{{ old('description', $room?->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="price_monthly" class="block font-bold text-sm uppercase tracking-wide mb-1">Monthly price (Rp)</label>
            <input id="price_monthly" name="price_monthly" type="number" min="0" value="{{ old('price_monthly', $room?->price_monthly) }}"
                   class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan" required>
            @error('price_monthly') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="price_yearly" class="block font-bold text-sm uppercase tracking-wide mb-1">Yearly price (Rp, optional)</label>
            <input id="price_yearly" name="price_yearly" type="number" min="0" value="{{ old('price_yearly', $room?->price_yearly) }}"
                   class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
            @error('price_yearly') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex flex-wrap gap-6">
        <label class="inline-flex items-center gap-2 font-bold">
            <input type="hidden" name="electricity_included" value="0">
            <input type="checkbox" name="electricity_included" value="1" class="border-2 border-black rounded"
                   @checked(old('electricity_included', $room?->electricity_included ?? false))>
            Electricity included
        </label>
        <label class="inline-flex items-center gap-2 font-bold">
            <input type="hidden" name="water_included" value="0">
            <input type="checkbox" name="water_included" value="1" class="border-2 border-black rounded"
                   @checked(old('water_included', $room?->water_included ?? false))>
            Water included
        </label>
        <label class="inline-flex items-center gap-2 font-bold">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="border-2 border-black rounded"
                   @checked(old('is_active', $room?->is_active ?? true))>
            Visible (active)
        </label>
    </div>

    <div>
        <span class="block font-bold text-sm uppercase tracking-wide mb-2">Facilities</span>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            @foreach ($facilities as $facility)
                <label class="inline-flex items-center gap-2 text-sm font-medium">
                    <input type="checkbox" name="facilities[]" value="{{ $facility->id }}" class="border-2 border-black rounded"
                           @checked(in_array($facility->id, $selectedFacilities, true))>
                    {{ $facility->name }}
                </label>
            @endforeach
        </div>
        @error('facilities') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
        @error('facilities.*') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
    </div>

    @if ($method === 'POST')
        <div>
            <label for="photos" class="block font-bold text-sm uppercase tracking-wide mb-1">Photos</label>
            <input id="photos" name="photos[]" type="file" multiple accept="image/*"
                   class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white file:mr-3 file:border-2 file:border-black file:rounded-md file:px-3 file:py-1 file:font-bold file:bg-yellow">
            @error('photos') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
            @error('photos.*') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
        </div>
    @endif

    <div class="flex items-center gap-3">
        <button type="submit"
                class="bg-cyan border-2 border-black rounded-lg px-5 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0 active:translate-y-0 active:shadow-none transition">
            {{ $submitLabel }}
        </button>
        <a href="{{ $cancelUrl }}" class="font-bold underline">Cancel</a>
    </div>
</form>

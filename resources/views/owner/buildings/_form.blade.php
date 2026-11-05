{{-- Expects: $building (nullable), $action, $method, $submitLabel, $cancelUrl --}}
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

    <div>
        <label for="name" class="block font-bold text-sm uppercase tracking-wide mb-1">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $building?->name) }}"
               class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan" required>
        @error('name') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="block font-bold text-sm uppercase tracking-wide mb-1">Description</label>
        <textarea id="description" name="description" rows="3"
                  class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">{{ old('description', $building?->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="street" class="block font-bold text-sm uppercase tracking-wide mb-1">Street</label>
            <input id="street" name="street" type="text" value="{{ old('street', $building?->street) }}"
                   class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan" required>
            @error('street') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="city" class="block font-bold text-sm uppercase tracking-wide mb-1">City</label>
            <input id="city" name="city" type="text" value="{{ old('city', $building?->city) }}"
                   class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan" required>
            @error('city') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="province" class="block font-bold text-sm uppercase tracking-wide mb-1">Province</label>
            <input id="province" name="province" type="text" value="{{ old('province', $building?->province) }}"
                   class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan" required>
            @error('province') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="house_rules" class="block font-bold text-sm uppercase tracking-wide mb-1">House Rules</label>
        <textarea id="house_rules" name="house_rules" rows="3"
                  class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">{{ old('house_rules', $building?->house_rules) }}</textarea>
        @error('house_rules') <p class="mt-1 text-sm font-bold text-coral">{{ $message }}</p> @enderror
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

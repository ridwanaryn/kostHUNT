<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">New Maintenance Request</h2>
            <p class="text-sm text-neutral-600">Tell your owner what needs fixing.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if ($bookings->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-8 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-lg font-black uppercase">No active bookings</p>
                    <p class="text-neutral-600 mt-2">You need an active booking to submit a maintenance request.</p>
                    <a href="{{ route('tenant.bookings.index') }}" class="inline-block mt-4 bg-cyan border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000]">View my bookings</a>
                </div>
            @else
                <form method="POST" action="{{ route('tenant.maintenance-requests.store') }}"
                      class="bg-white border-[3px] border-black rounded-2xl shadow-[6px_6px_0px_#000] p-6 space-y-4">
                    @csrf

                    @if ($errors->any())
                        <div class="border-2 border-black rounded-xl bg-coral text-white px-4 py-3 font-bold">Please fix the highlighted fields.</div>
                    @endif

                    <div>
                        <label for="booking_id" class="block text-xs font-black uppercase text-neutral-600 mb-1">Room</label>
                        <select id="booking_id" name="booking_id" class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white" required>
                            <option value="">Select your active room</option>
                            @foreach ($bookings as $booking)
                                <option value="{{ $booking->id }}" @selected((int) old('booking_id') === $booking->id)>
                                    {{ $booking->room->name }} &middot; {{ $booking->room->building->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('booking_id') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="title" class="block text-xs font-black uppercase text-neutral-600 mb-1">Title</label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}" maxlength="255"
                               class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white" required>
                        @error('title') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-black uppercase text-neutral-600 mb-1">Description</label>
                        <textarea id="description" name="description" rows="5" maxlength="2000"
                                  class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white" required>{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="bg-cyan border-2 border-black rounded-lg px-5 py-2 font-black uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                            Submit request
                        </button>
                        <a href="{{ route('tenant.maintenance-requests.index') }}" class="font-bold underline">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>

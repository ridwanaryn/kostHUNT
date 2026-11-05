<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">{{ $room->name }}</h2>
                    <span class="border-2 border-black rounded-full px-2 py-0.5 text-xs font-bold {{ $room->status()->badgeClasses() }}">
                        {{ $room->status()->label() }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">
                    <a href="{{ route('owner.buildings.show', $room->building) }}" class="underline">{{ $room->building->name }}</a>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('owner.rooms.edit', $room) }}"
                   class="bg-white border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('owner.rooms.toggle-active', $room) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="bg-lilac border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 transition">
                        {{ $room->is_active ? 'Hide room' : 'Show room' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('owner.rooms.destroy', $room) }}"
                      onsubmit="return confirm('Delete this room?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-coral text-white border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @error('delete')
                <div class="border-2 border-black rounded-xl bg-coral text-white px-4 py-3 font-bold shadow-[3px_3px_0px_#000]">
                    {{ $message }}
                </div>
            @enderror

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 bg-white border-2 border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6 space-y-4">
                    <div>
                        <h3 class="font-bold uppercase text-sm tracking-wide text-gray-500">Description</h3>
                        <p class="mt-1 text-gray-800">{{ $room->description ?: 'No description yet.' }}</p>
                    </div>
                    <div>
                        <h3 class="font-bold uppercase text-sm tracking-wide text-gray-500">Facilities</h3>
                        @if ($room->facilities->isEmpty())
                            <p class="mt-1 text-gray-800">No facilities selected.</p>
                        @else
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($room->facilities as $facility)
                                    <span class="border-2 border-black rounded-full px-3 py-1 text-sm font-bold bg-canvas">{{ $facility->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-yellow border-2 border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-bold uppercase">Details</h3>
                    <dl class="mt-3 space-y-2 text-sm font-bold">
                        <div class="flex justify-between"><dt>Monthly</dt><dd>{{ \App\Support\Money::format($room->price_monthly) }}</dd></div>
                        <div class="flex justify-between"><dt>Yearly</dt><dd>{{ $room->price_yearly ? \App\Support\Money::format($room->price_yearly) : '—' }}</dd></div>
                        <div class="flex justify-between"><dt>Size</dt><dd>{{ $room->size_sqm ? $room->size_sqm.' m²' : '—' }}</dd></div>
                        <div class="flex justify-between"><dt>Electricity</dt><dd>{{ $room->electricity_included ? 'Included' : 'Excluded' }}</dd></div>
                        <div class="flex justify-between"><dt>Water</dt><dd>{{ $room->water_included ? 'Included' : 'Excluded' }}</dd></div>
                    </dl>
                </div>
            </div>

            @include('owner.partials.photo-manager', [
                'photos' => $room->photos,
                'uploadRoute' => route('owner.rooms.photos.store', $room),
            ])
        </div>
    </div>
</x-app-layout>

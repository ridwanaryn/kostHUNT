<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">{{ $building->name }}</h2>
                <p class="text-sm text-gray-600">{{ $building->street }}, {{ $building->city }}, {{ $building->province }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('owner.buildings.tenants', $building) }}"
                   class="bg-white border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 transition">
                    Tenants
                </a>
                <a href="{{ route('owner.buildings.edit', $building) }}"
                   class="bg-white border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 transition">
                    Edit
                </a>
                <a href="{{ route('owner.rooms.create', $building) }}"
                   class="bg-yellow border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 transition">
                    + Add room
                </a>
                <form method="POST" action="{{ route('owner.buildings.destroy', $building) }}"
                      onsubmit="return confirm('Delete this building?');">
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
                        <p class="mt-1 text-gray-800">{{ $building->description ?: 'No description yet.' }}</p>
                    </div>
                    <div>
                        <h3 class="font-bold uppercase text-sm tracking-wide text-gray-500">House rules</h3>
                        <p class="mt-1 text-gray-800 whitespace-pre-line">{{ $building->house_rules ?: 'No house rules set.' }}</p>
                    </div>
                </div>

                <div class="bg-yellow border-2 border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-bold uppercase">Summary</h3>
                    <dl class="mt-3 space-y-2 text-sm font-bold">
                        <div class="flex justify-between"><dt>Rooms</dt><dd>{{ $building->rooms->count() }}</dd></div>
                        <div class="flex justify-between"><dt>Photos</dt><dd>{{ $building->photos->count() }}</dd></div>
                    </dl>
                </div>
            </div>

            @include('owner.partials.photo-manager', [
                'photos' => $building->photos,
                'uploadRoute' => route('owner.buildings.photos.store', $building),
            ])

            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-display text-xl font-black uppercase">Rooms</h3>
                    <a href="{{ route('owner.rooms.create', $building) }}" class="font-bold underline">+ Add room</a>
                </div>

                @if ($building->rooms->isEmpty())
                    <div class="bg-white border-2 border-black rounded-2xl p-8 text-center shadow-[4px_4px_0px_#000]">
                        <p class="font-bold">No rooms yet.</p>
                    </div>
                @else
                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($building->rooms as $room)
                            <a href="{{ route('owner.rooms.show', $room) }}"
                               class="bg-white border-2 border-black rounded-2xl shadow-[4px_4px_0px_#000] overflow-hidden hover:-translate-y-1 hover:shadow-[6px_6px_0px_#000] transition">
                                @if ($cover = $room->coverPhoto())
                                    <img src="{{ $cover->url() }}" alt="{{ $room->name }}" class="w-full h-32 object-cover border-b-2 border-black">
                                @else
                                    <div class="w-full h-32 bg-lilac border-b-2 border-black flex items-center justify-center font-display font-bold uppercase">No photo</div>
                                @endif
                                <div class="p-4">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-display font-black uppercase">{{ $room->name }}</h4>
                                        <span class="border-2 border-black rounded-full px-2 py-0.5 text-xs font-bold {{ $room->status()->badgeClasses() }}">
                                            {{ $room->status()->label() }}
                                        </span>
                                    </div>
                                    <p class="mt-2 font-bold">{{ \App\Support\Money::format($room->price_monthly) }}<span class="text-sm font-normal text-gray-600"> / month</span></p>
                                    @if ($room->size_sqm)
                                        <p class="text-sm text-gray-600">{{ $room->size_sqm }} m&sup2;</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>

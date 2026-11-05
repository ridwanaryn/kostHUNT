<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">My Buildings</h2>
            <a href="{{ route('owner.buildings.create') }}"
               class="bg-yellow border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0 active:translate-y-0 active:shadow-none transition">
                + New Building
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($buildings->isEmpty())
                <div class="bg-white border-2 border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-bold uppercase">No buildings yet</p>
                    <p class="text-gray-600 mt-2">Add your first property to start listing rooms.</p>
                    <a href="{{ route('owner.buildings.create') }}"
                       class="inline-block mt-5 bg-cyan border-2 border-black rounded-lg px-5 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 transition">
                        Create a building
                    </a>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($buildings as $building)
                        <a href="{{ route('owner.buildings.show', $building) }}"
                           class="block bg-white border-2 border-black rounded-2xl shadow-[4px_4px_0px_#000] overflow-hidden hover:-translate-y-1 hover:shadow-[6px_6px_0px_#000] transition">
                            @if ($cover = $building->coverPhoto())
                                <img src="{{ $cover->url() }}" alt="{{ $building->name }}" class="w-full h-40 object-cover border-b-2 border-black">
                            @else
                                <div class="w-full h-40 bg-lilac border-b-2 border-black flex items-center justify-center font-display text-lg font-bold uppercase">No photo</div>
                            @endif
                            <div class="p-5">
                                <h3 class="font-display text-lg font-black uppercase">{{ $building->name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $building->street }}, {{ $building->city }}, {{ $building->province }}</p>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold">
                                    <span class="border-2 border-black rounded-full px-2 py-0.5 bg-yellow">{{ $building->rooms_count }} rooms</span>
                                    <span class="border-2 border-black rounded-full px-2 py-0.5 bg-mint">{{ $building->available_rooms_count }} available</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $buildings->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

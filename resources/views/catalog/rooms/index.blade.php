<x-public-layout title="Search rooms">
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <nav class="text-xs font-bold uppercase text-neutral-600">
            <a href="{{ route('home') }}" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <span>Rooms</span>
        </nav>
        <h1 class="font-display text-3xl sm:text-4xl font-black uppercase mt-2">Find a room</h1>
    </section>

    @php
        $selectedFacilities = array_map('intval', (array) ($filters['facilities'] ?? []));
        $sort = $filters['sort'] ?? 'newest';
    @endphp

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 grid gap-8 lg:grid-cols-[300px_1fr]">
        <aside>
            <form action="{{ route('rooms.index') }}" method="GET"
                  class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 space-y-4 lg:sticky lg:top-28">
                <h2 class="font-display text-lg font-black uppercase">Filters</h2>

                <div>
                    <label for="province" class="block text-xs font-bold uppercase tracking-wide mb-1">Province</label>
                    <input id="province" name="province" type="text" value="{{ $filters['province'] ?? '' }}"
                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
                </div>

                <div>
                    <label for="city" class="block text-xs font-bold uppercase tracking-wide mb-1">City</label>
                    <input id="city" name="city" type="text" value="{{ $filters['city'] ?? '' }}"
                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="min_price" class="block text-xs font-bold uppercase tracking-wide mb-1">Min (Rp)</label>
                        <input id="min_price" name="min_price" type="number" min="0" value="{{ $filters['min_price'] ?? '' }}"
                               class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
                    </div>
                    <div>
                        <label for="max_price" class="block text-xs font-bold uppercase tracking-wide mb-1">Max (Rp)</label>
                        <input id="max_price" name="max_price" type="number" min="0" value="{{ $filters['max_price'] ?? '' }}"
                               class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
                    </div>
                </div>

                <div>
                    <label for="start_date" class="block text-xs font-bold uppercase tracking-wide mb-1">Available from</label>
                    <input id="start_date" name="start_date" type="date" value="{{ $filters['start_date'] ?? '' }}"
                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
                </div>

                <div>
                    <span class="block text-xs font-bold uppercase tracking-wide mb-2">Facilities</span>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        @foreach ($facilities as $facility)
                            <label class="flex items-center gap-2 text-sm font-medium">
                                <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                       class="border-2 border-black rounded" @checked(in_array($facility->id, $selectedFacilities, true))>
                                {{ $facility->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="sort" class="block text-xs font-bold uppercase tracking-wide mb-1">Sort by</label>
                    <select id="sort" name="sort"
                            class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
                        <option value="newest" @selected($sort === 'newest')>Newest</option>
                        <option value="price_asc" @selected($sort === 'price_asc')>Price: low to high</option>
                        <option value="price_desc" @selected($sort === 'price_desc')>Price: high to low</option>
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <button type="submit"
                            class="flex-1 bg-cyan border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 active:translate-y-0 active:shadow-none transition">
                        Apply
                    </button>
                    <a href="{{ route('rooms.index') }}" class="font-bold underline">Reset</a>
                </div>
            </form>
        </aside>

        <div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="font-bold">
                    <span class="font-display text-xl font-black">{{ $rooms->total() }}</span> room(s) found
                </p>
            </div>

            @if ($rooms->isEmpty())
                <div class="mt-6 bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No rooms match your filters</p>
                    <p class="text-neutral-600 mt-2">Try widening your budget or removing a facility.</p>
                    <a href="{{ route('rooms.index') }}" class="inline-block mt-4 bg-yellow border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000]">See all rooms</a>
                </div>
            @else
                <div class="mt-5 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($rooms as $room)
                        <x-room-card :room="$room" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $rooms->links() }}
                </div>
            @endif
        </div>
    </section>
</x-public-layout>

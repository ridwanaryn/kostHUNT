<x-public-layout :title="$building->name">
    @php $cover = $building->coverPhoto(); @endphp

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <nav class="text-xs font-bold uppercase text-neutral-600">
            <a href="{{ route('home') }}" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('rooms.index') }}" class="hover:underline">Rooms</a>
            <span class="mx-2">/</span>
            <span>{{ $building->name }}</span>
        </nav>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="grid gap-8 lg:grid-cols-[1.4fr_1fr]">
            <div>
                @if ($cover)
                    <img src="{{ $cover->url() }}" alt="{{ $building->name }}" class="w-full h-72 sm:h-96 object-cover border-[4px] border-black rounded-3xl shadow-[8px_8px_0px_#000]">
                @else
                    <div class="w-full h-72 sm:h-96 bg-lilac border-[4px] border-black rounded-3xl shadow-[8px_8px_0px_#000] flex items-center justify-center font-display text-xl font-black uppercase">
                        No photo yet
                    </div>
                @endif

                @if ($building->photos->count() > 1)
                    <div class="mt-4 flex gap-3 overflow-x-auto pb-2">
                        @foreach ($building->photos as $photo)
                            <img src="{{ $photo->url() }}" alt="Photo of {{ $building->name }}"
                                 class="h-20 w-28 object-cover border-2 border-black rounded-xl shrink-0">
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                <h1 class="font-display text-3xl font-black uppercase leading-tight">{{ $building->name }}</h1>
                <p class="text-neutral-700 font-medium mt-1">{{ $building->street }}, {{ $building->city }}, {{ $building->province }}</p>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-bold bg-mint">{{ $building->rooms->count() }} rooms available</span>
                    @if (($building->reviews_count ?? 0) > 0)
                        <x-rating-stars :rating="$building->reviews_avg_rating" :count="$building->reviews_count" />
                    @endif
                </div>

                <h2 class="font-display text-lg font-black uppercase mt-5">About</h2>
                <p class="text-neutral-800 whitespace-pre-line">{{ $building->description ?: 'No description provided.' }}</p>

                @if ($building->house_rules)
                    <h2 class="font-display text-lg font-black uppercase mt-5">House rules</h2>
                    <p class="text-neutral-800 whitespace-pre-line">{{ $building->house_rules }}</p>
                @endif
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="flex items-end justify-between gap-4">
            <h2 class="font-display text-2xl sm:text-3xl font-black uppercase">Rooms in this building</h2>
            <a href="{{ route('rooms.index') }}" class="font-bold underline underline-offset-4">Back to search</a>
        </div>

        @if ($building->rooms->isEmpty())
            <div class="mt-6 bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                <p class="font-display text-xl font-black uppercase">No available rooms</p>
                <p class="text-neutral-600 mt-2">This building has no visible rooms right now.</p>
            </div>
        @else
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($building->rooms as $room)
                    <x-room-card :room="$room" />
                @endforeach
            </div>
        @endif
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <h2 class="font-display text-2xl sm:text-3xl font-black uppercase">Reviews</h2>
        @if ($reviews->isEmpty())
            <div class="mt-4 bg-white border-[3px] border-black rounded-2xl p-8 text-center shadow-[4px_4px_0px_#000]">
                <p class="text-neutral-600">No reviews for this building yet.</p>
            </div>
        @else
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach ($reviews as $review)
                    <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-bold">{{ $review->tenant->name }}</p>
                            <x-rating-stars :rating="$review->rating" />
                        </div>
                        <p class="text-xs font-bold text-neutral-500 mt-0.5">{{ $review->room->name }}</p>
                        @if ($review->comment)
                            <p class="mt-2 text-neutral-800">{{ $review->comment }}</p>
                        @endif
                        <p class="mt-2 text-xs font-bold text-neutral-500">{{ $review->created_at->format('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-public-layout>

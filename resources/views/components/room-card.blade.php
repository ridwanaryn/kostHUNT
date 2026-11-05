@props(['room'])

@php
    $cover = $room->coverPhoto();
    $reviewCount = $room->reviews_count ?? 0;
    $averageRating = $room->reviews_avg_rating;
@endphp

<a href="{{ route('rooms.show', $room) }}"
   class="group flex flex-col bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] overflow-hidden hover:-translate-y-1 hover:shadow-[6px_6px_0px_#000] transition">
    <div class="relative">
        @if ($cover)
            <img src="{{ $cover->url() }}" alt="{{ $room->name }}" class="w-full h-44 object-cover border-b-[3px] border-black">
        @else
            <div class="w-full h-44 bg-lilac border-b-[3px] border-black flex items-center justify-center font-display font-black uppercase text-sm">No photo</div>
        @endif

        <span class="absolute top-2 left-2 border-2 border-black rounded-full px-2 py-0.5 text-[10px] font-black uppercase {{ $room->status()->badgeClasses() }}">
            {{ $room->status()->label() }}
        </span>
        <span class="absolute bottom-2 right-2 bg-yellow border-2 border-black rounded-lg px-2 py-1 text-sm font-black shadow-[2px_2px_0px_#000]">
            {{ \App\Support\Money::format($room->price_monthly) }}<span class="text-[10px] font-bold">/mo</span>
        </span>
    </div>

    <div class="p-4 flex flex-col flex-1">
        <h3 class="font-display text-lg font-black uppercase leading-tight">{{ $room->name }}</h3>
        <p class="text-sm text-neutral-600 mt-0.5">{{ $room->building->name }} &middot; {{ $room->building->city }}</p>

        @if ($room->facilities->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-1.5">
                @foreach ($room->facilities->take(3) as $facility)
                    <span class="border border-black rounded-full px-2 py-0.5 text-[10px] font-bold bg-canvas">{{ $facility->name }}</span>
                @endforeach
            </div>
        @endif

        <div class="mt-auto pt-3 flex items-center justify-between">
            @if ($reviewCount > 0)
                <x-rating-stars :rating="$averageRating" :count="$reviewCount" />
            @else
                <span class="text-xs font-bold text-neutral-600">New</span>
            @endif
            <span class="text-xs font-black uppercase group-hover:underline">View room &rarr;</span>
        </div>
    </div>
</a>

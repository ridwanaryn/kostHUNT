<x-public-layout title="Find your next kost">
    {{-- Hero --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 lg:pt-16">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <span class="inline-block bg-pink border-2 border-black rounded-full px-3 py-1 text-xs font-black uppercase shadow-[2px_2px_0px_#000]">
                    Gen-Z approved
                </span>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-black uppercase leading-[0.95] tracking-tight mt-4">
                    Find your next kost.<br>
                    <span class="bg-yellow px-2 inline-block mt-1">Zero drama.</span>
                </h1>
                <p class="mt-5 text-neutral-700 font-medium max-w-md">
                    Verified rooms, transparent prices, and instant booking. Built for students and young professionals.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('rooms.index') }}"
                       class="bg-cyan border-[3px] border-black rounded-xl px-5 py-3 font-black uppercase shadow-[4px_4px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0 active:translate-y-0 active:shadow-none transition">
                        Browse rooms
                    </a>
                    <a href="{{ route('home') }}#why"
                       class="bg-white border-[3px] border-black rounded-xl px-5 py-3 font-black uppercase shadow-[4px_4px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 transition">
                        Why kostHUNT?
                    </a>
                </div>
            </div>

            <div class="bg-white border-[4px] border-black rounded-3xl shadow-[8px_8px_0px_#000] p-5 sm:p-6">
                <h2 class="font-display text-xl font-black uppercase">Super fast search filter</h2>
                <form action="{{ route('rooms.index') }}" method="GET" class="mt-4 space-y-3">
                    <div>
                        <label for="home-city" class="block text-xs font-bold uppercase tracking-wide mb-1">Location / campus</label>
                        <input id="home-city" name="city" type="text" placeholder="Bandung, Depok, ..."
                               class="w-full border-[3px] border-black rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
                    </div>
                    <div>
                        <label for="home-budget" class="block text-xs font-bold uppercase tracking-wide mb-1">Monthly budget</label>
                        <select id="home-budget" name="max_price"
                                class="w-full border-[3px] border-black rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-cyan">
                            <option value="">Any budget</option>
                            <option value="1500000">Under Rp 1.500.000</option>
                            <option value="2500000">Under Rp 2.500.000</option>
                            <option value="3500000">Under Rp 3.500.000</option>
                            <option value="5000000">Under Rp 5.000.000</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full bg-cyan border-[3px] border-black rounded-xl px-4 py-3 font-black uppercase shadow-[4px_4px_0px_#000] hover:-translate-y-0.5 active:translate-y-0 active:shadow-none transition">
                        Search rooms
                    </button>
                </form>

                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($facilities as $facility)
                        <a href="{{ route('rooms.index', ['facilities' => [$facility->id]]) }}"
                           class="border-2 border-black rounded-full px-2.5 py-1 text-[11px] font-bold bg-canvas hover:bg-yellow transition">
                            &#10003; {{ $facility->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Latest rooms bento --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        <div class="flex items-end justify-between gap-4">
            <h2 class="font-display text-2xl sm:text-3xl font-black uppercase">Top recommendations</h2>
            <a href="{{ route('rooms.index') }}" class="font-bold underline underline-offset-4">View all</a>
        </div>

        @if ($rooms->isEmpty())
            <div class="mt-6 bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                <p class="font-display text-xl font-black uppercase">No rooms yet</p>
                <p class="text-neutral-600 mt-2">Owners haven't listed any rooms yet. Check back soon.</p>
            </div>
        @else
            @php $featured = $rooms->first(); @endphp
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <a href="{{ route('rooms.show', $featured) }}"
                   class="sm:col-span-2 lg:row-span-2 flex flex-col bg-white border-[4px] border-black rounded-3xl shadow-[8px_8px_0px_#000] overflow-hidden hover:-translate-y-1 hover:shadow-[10px_10px_0px_#000] transition">
                    <div class="relative">
                        @if ($cover = $featured->coverPhoto())
                            <img src="{{ $cover->url() }}" alt="{{ $featured->name }}" class="w-full h-56 object-cover border-b-[3px] border-black">
                        @else
                            <div class="w-full h-56 bg-lilac border-b-[3px] border-black flex items-center justify-center font-display font-black uppercase">No photo</div>
                        @endif
                        <span class="absolute top-3 left-3 bg-pink border-2 border-black rounded-full px-3 py-1 text-[10px] font-black uppercase shadow-[2px_2px_0px_#000]">
                            Top recommendation
                        </span>
                        <span class="absolute bottom-3 right-3 bg-yellow border-[3px] border-black rounded-xl px-3 py-1.5 font-black shadow-[3px_3px_0px_#000]">
                            {{ \App\Support\Money::format($featured->price_monthly) }}<span class="text-xs">/mo</span>
                        </span>
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-display text-2xl font-black uppercase leading-tight">{{ $featured->name }}</h3>
                        <p class="text-sm text-neutral-600 mt-1">{{ $featured->building->name }} &middot; {{ $featured->building->city }}, {{ $featured->building->province }}</p>

                        @if ($featured->facilities->isNotEmpty())
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                @foreach ($featured->facilities->take(6) as $facility)
                                    <span class="border-2 border-black rounded-lg px-2 py-1 text-xs font-bold bg-canvas">{{ $facility->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-auto pt-5 flex flex-wrap gap-3">
                            <span class="bg-white border-[3px] border-black rounded-xl px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000]">
                                View details
                            </span>
                            <span class="bg-yellow border-[3px] border-black rounded-xl px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000]">
                                Book now
                            </span>
                        </div>
                    </div>
                </a>

                @foreach ($rooms->skip(1) as $room)
                    <x-room-card :room="$room" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Info bento --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 grid gap-5 md:grid-cols-3">
        <div class="bg-mint border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
            <h3 class="font-display text-lg font-black uppercase">Popular cities</h3>
            <ul class="mt-3 space-y-2">
                @forelse ($cities as $city)
                    <li>
                        <a href="{{ route('rooms.index', ['city' => $city->city]) }}"
                           class="flex items-center justify-between border-2 border-black rounded-lg px-3 py-1.5 bg-white font-bold text-sm hover:bg-yellow transition">
                            <span>{{ $city->city }}</span>
                            <span class="border-2 border-black rounded-full px-2 text-xs">{{ $city->buildings_count }}</span>
                        </a>
                    </li>
                @empty
                    <li class="text-sm font-bold">No cities yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-yellow border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 flex flex-col justify-between">
            <div>
                <span class="inline-flex items-center gap-2 text-xs font-black uppercase">
                    <span class="w-2.5 h-2.5 rounded-full bg-coral border border-black animate-pulse"></span> Live now
                </span>
                <p class="font-display text-5xl font-black mt-2">{{ $availableRooms }}</p>
                <p class="text-sm font-bold">exclusive rooms available</p>
            </div>
            <a href="{{ route('rooms.index') }}" class="mt-4 inline-block font-black uppercase underline underline-offset-4">Grab one &rarr;</a>
        </div>

        <div class="bg-coral border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 text-white" x-data="{ copied: false }">
            <span class="inline-block bg-white text-onyx border-2 border-black rounded-full px-3 py-1 text-[10px] font-black uppercase">Hot promo</span>
            <h3 class="font-display text-lg font-black uppercase mt-3">Student ticket</h3>
            <p class="text-sm font-medium">Use this code for your move-in perk.</p>
            <p class="font-display text-2xl font-black tracking-widest mt-3">kostHUNT20</p>
            <button type="button"
                    @click="navigator.clipboard.writeText('kostHUNT20'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="mt-3 bg-white text-onyx border-2 border-black rounded-lg px-3 py-1.5 text-sm font-bold uppercase shadow-[2px_2px_0px_#000] hover:-translate-y-0.5 transition">
                <span x-show="! copied">Copy code</span>
                <span x-show="copied" x-cloak>Copied!</span>
            </button>
        </div>
    </section>

    {{-- Facility spotlight --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-lilac border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
            <h3 class="font-display text-lg font-black uppercase">Everything students actually need</h3>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($facilities as $facility)
                    <span class="bg-white border-2 border-black rounded-full px-3 py-1 text-sm font-bold">{{ $facility->name }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Value props --}}
    <section id="why" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        <h2 class="font-display text-2xl sm:text-3xl font-black uppercase">Why kostHUNT!</h2>
        <div class="mt-6 grid gap-5 md:grid-cols-3">
            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                <div class="w-10 h-10 bg-mint border-2 border-black rounded-lg flex items-center justify-center font-black">100%</div>
                <h3 class="font-display text-lg font-black uppercase mt-3">Verified &amp; real</h3>
                <p class="text-sm text-neutral-700 mt-1">Every room is checked so you know what you're getting.</p>
            </div>
            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                <div class="w-10 h-10 bg-yellow border-2 border-black rounded-lg flex items-center justify-center font-black">0</div>
                <h3 class="font-display text-lg font-black uppercase mt-3">Zero admin fees</h3>
                <p class="text-sm text-neutral-700 mt-1">Transparent prices with no surprise admin charges.</p>
            </div>
            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                <div class="w-10 h-10 bg-pink border-2 border-black rounded-lg flex items-center justify-center font-black">&#9829;</div>
                <h3 class="font-display text-lg font-black uppercase mt-3">Fun community</h3>
                <p class="text-sm text-neutral-700 mt-1">Chat directly with owners and settle in with confidence.</p>
            </div>
        </div>
    </section>
</x-public-layout>

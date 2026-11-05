<x-public-layout :title="$room->name.' — '.$room->building->name">
    @php
        $cover = $room->coverPhoto();
        $gallery = $room->photos;
    @endphp

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <nav class="text-xs font-bold uppercase text-neutral-600">
            <a href="{{ route('home') }}" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('rooms.index') }}" class="hover:underline">Rooms</a>
            <span class="mx-2">/</span>
            <span>{{ $room->name }}</span>
        </nav>

        <div class="mt-3 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="font-display text-3xl sm:text-4xl font-black uppercase leading-tight">{{ $room->name }}</h1>
                <p class="text-neutral-600 font-medium mt-1">
                    <a href="{{ route('buildings.show', $room->building) }}" class="underline underline-offset-4">{{ $room->building->name }}</a>
                    &middot; {{ $room->building->street }}, {{ $room->building->city }}, {{ $room->building->province }}
                </p>
            </div>
            <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-black uppercase {{ $room->status()->badgeClasses() }}">
                {{ $room->status()->label() }}
            </span>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 grid gap-8 lg:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            {{-- Gallery --}}
            <div>
                @if ($cover)
                    <img src="{{ $cover->url() }}" alt="{{ $room->name }}" class="w-full h-72 sm:h-96 object-cover border-[4px] border-black rounded-3xl shadow-[6px_6px_0px_#000]">
                @else
                    <div class="w-full h-72 sm:h-96 bg-lilac border-[4px] border-black rounded-3xl shadow-[6px_6px_0px_#000] flex items-center justify-center font-display text-xl font-black uppercase">
                        No photo yet
                    </div>
                @endif

                @if ($gallery->count() > 1)
                    <div class="mt-4 flex gap-3 overflow-x-auto pb-2">
                        @foreach ($gallery as $photo)
                            <img src="{{ $photo->url() }}" alt="Photo of {{ $room->name }}"
                                 class="h-20 w-28 object-cover border-2 border-black rounded-xl shrink-0">
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Description --}}
            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                <h2 class="font-display text-xl font-black uppercase">About this room</h2>
                <p class="mt-2 text-neutral-800 whitespace-pre-line">{{ $room->description ?: 'No description provided.' }}</p>

                <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="border-2 border-black rounded-xl p-3 bg-canvas">
                        <p class="text-[10px] font-black uppercase text-neutral-600">Size</p>
                        <p class="font-bold">{{ $room->size_sqm ? $room->size_sqm.' m²' : '—' }}</p>
                    </div>
                    <div class="border-2 border-black rounded-xl p-3 bg-canvas">
                        <p class="text-[10px] font-black uppercase text-neutral-600">Electricity</p>
                        <p class="font-bold">{{ $room->electricity_included ? 'Included' : 'Excluded' }}</p>
                    </div>
                    <div class="border-2 border-black rounded-xl p-3 bg-canvas">
                        <p class="text-[10px] font-black uppercase text-neutral-600">Water</p>
                        <p class="font-bold">{{ $room->water_included ? 'Included' : 'Excluded' }}</p>
                    </div>
                    <div class="border-2 border-black rounded-xl p-3 bg-canvas">
                        <p class="text-[10px] font-black uppercase text-neutral-600">Yearly</p>
                        <p class="font-bold">{{ $room->price_yearly ? \App\Support\Money::format($room->price_yearly) : '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Facilities --}}
            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                <h2 class="font-display text-xl font-black uppercase">Facilities</h2>
                @if ($room->facilities->isEmpty())
                    <p class="mt-2 text-neutral-600">No facilities listed.</p>
                @else
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($room->facilities as $facility)
                            <span class="border-2 border-black rounded-full px-3 py-1 text-sm font-bold bg-mint">{{ $facility->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Building --}}
            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                <h2 class="font-display text-xl font-black uppercase">Building &amp; house rules</h2>
                <p class="mt-2 font-bold">{{ $room->building->name }}</p>
                <p class="text-neutral-700">{{ $room->building->street }}, {{ $room->building->city }}, {{ $room->building->province }}</p>
                @if ($room->building->house_rules)
                    <p class="mt-3 text-neutral-800 whitespace-pre-line">{{ $room->building->house_rules }}</p>
                @endif
                <a href="{{ route('buildings.show', $room->building) }}" class="inline-block mt-4 font-bold underline underline-offset-4">See all rooms in this building &rarr;</a>
            </div>

            {{-- Reviews --}}
            @php
                $reviewCount = $room->reviews->count();
                $averageRating = $reviewCount > 0 ? $room->reviews->avg('rating') : 0;
            @endphp
            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-display text-xl font-black uppercase">Reviews</h2>
                    @if ($reviewCount > 0)
                        <x-rating-stars :rating="$averageRating" :count="$reviewCount" />
                    @endif
                </div>

                @if ($reviewCount === 0)
                    <p class="mt-2 text-neutral-600">No reviews yet. Reviews from verified tenants will appear here.</p>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach ($room->reviews->sortByDesc('created_at') as $review)
                            <div class="border-2 border-black rounded-xl p-4 bg-canvas">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="font-bold">{{ $review->tenant->name }}</p>
                                    <x-rating-stars :rating="$review->rating" />
                                </div>
                                @if ($review->comment)
                                    <p class="mt-2 text-neutral-800">{{ $review->comment }}</p>
                                @endif
                                <p class="mt-1 text-xs font-bold text-neutral-500">{{ $review->created_at->format('d M Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                @can('create', [\App\Models\Review::class, $room])
                    <form method="POST" action="{{ route('tenant.reviews.store', $room) }}"
                          class="mt-6 border-2 border-black rounded-xl p-4 bg-canvas space-y-3">
                        @csrf
                        @error('review')
                            <p class="text-sm font-bold text-coral">{{ $message }}</p>
                        @enderror
                        <div>
                            <label for="rating" class="block text-xs font-black uppercase text-neutral-600 mb-1">Your rating</label>
                            <select id="rating" name="rating" required class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                                <option value="">Select a rating</option>
                                @for ($star = 5; $star >= 1; $star--)
                                    <option value="{{ $star }}" @selected((int) old('rating') === $star)>{{ $star }} star{{ $star > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                            @error('rating') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="comment" class="block text-xs font-black uppercase text-neutral-600 mb-1">Comment (optional)</label>
                            <textarea id="comment" name="comment" rows="3"
                                      class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">{{ old('comment') }}</textarea>
                            @error('comment') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="bg-yellow border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                            Submit review
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        {{-- Booking card --}}
        <aside>
            <div class="bg-white border-[4px] border-black rounded-3xl shadow-[8px_8px_0px_#000] p-6 lg:sticky lg:top-28">
                <p class="text-xs font-black uppercase text-neutral-600">Monthly rent</p>
                <p class="font-display text-4xl font-black mt-1">{{ \App\Support\Money::format($room->price_monthly) }}</p>
                @if ($room->price_yearly)
                    <p class="text-sm font-bold text-neutral-600">or {{ \App\Support\Money::format($room->price_yearly) }} / year</p>
                @endif

                <div class="mt-4">
                    <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-black uppercase {{ $room->status()->badgeClasses() }}">
                        {{ $room->status()->label() }}
                    </span>
                </div>

                <div class="mt-5">
                    @auth
                        @if (auth()->user()->isTenant())
                            <form method="POST" action="{{ route('tenant.bookings.store', $room) }}" class="space-y-3"
                                  x-data="{
                                      period: 'monthly',
                                      duration: 1,
                                      priceMonthly: {{ (int) $room->price_monthly }},
                                      priceYearly: {{ $room->price_yearly !== null ? (int) $room->price_yearly : 'null' }},
                                      get price() { return this.period === 'yearly' ? (this.priceYearly ?? 0) : this.priceMonthly; },
                                      get total() { return this.price * (parseInt(this.duration) || 0); },
                                      format(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); }
                                  }">
                                @csrf

                                @error('booking')
                                    <div class="border-2 border-black rounded-lg bg-coral text-white px-3 py-2 text-sm font-bold">{{ $message }}</div>
                                @enderror

                                <div>
                                    <label for="start_date" class="block text-xs font-black uppercase text-neutral-600 mb-1">Start date</label>
                                    <input id="start_date" name="start_date" type="date"
                                           min="{{ now()->toDateString() }}"
                                           max="{{ now()->addDays((int) config('kosthunt.booking.advance_days'))->toDateString() }}"
                                           value="{{ old('start_date') }}" required
                                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                                    @error('start_date') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="period_type" class="block text-xs font-black uppercase text-neutral-600 mb-1">Rental period</label>
                                    <select id="period_type" name="period_type" x-model="period"
                                            class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                                        <option value="monthly">Monthly</option>
                                        @if ($room->price_yearly)
                                            <option value="yearly">Yearly</option>
                                        @endif
                                    </select>
                                    @error('period_type') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="duration" class="block text-xs font-black uppercase text-neutral-600 mb-1">Duration</label>
                                    <input id="duration" name="duration" type="number" min="1"
                                           max="{{ (int) config('kosthunt.booking.max_duration') }}"
                                           x-model="duration" value="{{ old('duration', 1) }}" required
                                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                                    @error('duration') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                                </div>

                                <div class="border-2 border-black rounded-xl p-3 bg-canvas">
                                    <div class="flex items-center justify-between text-sm font-black uppercase">
                                        <span>Total</span>
                                        <span x-text="format(total)">Rp 0</span>
                                    </div>
                                    <p class="text-[10px] font-bold text-neutral-600 mt-1">Final amount is verified on the server.</p>
                                </div>

                                <button type="submit"
                                        class="w-full bg-yellow border-[3px] border-black rounded-xl px-4 py-3 font-black uppercase shadow-[4px_4px_0px_#000] hover:-translate-y-0.5 active:translate-y-0 active:shadow-none transition">
                                    Confirm booking
                                </button>
                            </form>
                        @else
                            <p class="text-sm font-bold text-neutral-600 text-center">Owners cannot book rooms.</p>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="block text-center bg-yellow border-[3px] border-black rounded-xl px-4 py-3 font-black uppercase shadow-[4px_4px_0px_#000] hover:-translate-y-0.5 active:translate-y-0 active:shadow-none transition">
                            Log in to book
                        </a>
                    @endauth
                </div>

                @auth
                    @if (auth()->user()->isTenant())
                        <form method="POST"
                              action="{{ $isFavorited ? route('tenant.favorites.destroy', $room) : route('tenant.favorites.store', $room) }}"
                              class="mt-3">
                            @csrf
                            @if ($isFavorited)
                                @method('DELETE')
                            @endif
                            <button type="submit"
                                    class="w-full border-2 border-black rounded-xl px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition {{ $isFavorited ? 'bg-pink' : 'bg-white' }}">
                                {{ $isFavorited ? '♥ Saved to favorites' : '♡ Save to favorites' }}
                            </button>
                        </form>
                    @endif
                @endauth

                @auth
                    @if (auth()->user()->isTenant())
                        <form method="POST" action="{{ route('tenant.chat.start', $room) }}" class="mt-3">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-lilac border-2 border-black rounded-xl px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                                Chat with owner
                            </button>
                        </form>
                    @endif
                @endauth

                <ul class="mt-5 space-y-2 text-sm font-medium text-neutral-700">
                    <li>&#10003; Verified listing</li>
                    <li>&#10003; Zero admin fees</li>
                    <li>&#10003; No online payment required</li>
                </ul>
            </div>
        </aside>
    </section>
</x-public-layout>

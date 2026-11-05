<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">My Bookings</h2>
            <a href="{{ route('rooms.index') }}"
               class="bg-yellow border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                Browse rooms
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if ($bookings->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No bookings yet</p>
                    <p class="text-neutral-600 mt-2">Find a room and book it instantly.</p>
                    <a href="{{ route('rooms.index') }}" class="inline-block mt-4 bg-cyan border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000]">Find rooms</a>
                </div>
            @else
                <div class="grid gap-4">
                    @foreach ($bookings as $booking)
                        <a href="{{ route('tenant.bookings.show', $booking) }}"
                           class="block bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_#000] transition">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-display text-lg font-black uppercase">{{ $booking->room->name }}</h3>
                                    <p class="text-sm text-neutral-600">{{ $booking->room->building->name }} &middot; {{ $booking->room->building->city }}</p>
                                </div>
                                <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-black uppercase {{ $booking->status->badgeClasses() }}">
                                    {{ $booking->status->label() }}
                                </span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-sm font-bold">
                                <span>{{ $booking->start_date->format('d M Y') }} &rarr; {{ $booking->end_date->format('d M Y') }}</span>
                                <span>{{ $booking->period_type->label() }} &times; {{ $booking->duration }}</span>
                                <span>{{ \App\Support\Money::format($booking->rent_amount) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

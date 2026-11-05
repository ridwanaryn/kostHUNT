<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Tenants</h2>
                <p class="text-sm text-neutral-600">{{ $building->name }} &middot; {{ $building->city }}</p>
            </div>
            <a href="{{ route('owner.buildings.show', $building) }}" class="font-bold underline underline-offset-4">Back to building</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($bookings->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No tenants yet</p>
                </div>
            @else
                <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left font-black uppercase text-xs text-neutral-600 border-b-2 border-black">
                                <th class="p-4">Tenant</th>
                                <th class="p-4">Room</th>
                                <th class="p-4">Dates</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr class="border-b border-neutral-200">
                                    <td class="p-4">
                                        <p class="font-bold">{{ $booking->tenant->name }}</p>
                                        <p class="text-neutral-600">{{ $booking->tenant->email }}</p>
                                    </td>
                                    <td class="p-4 font-bold">{{ $booking->room->name }}</td>
                                    <td class="p-4">{{ $booking->start_date->format('d M Y') }} &rarr; {{ $booking->end_date->format('d M Y') }}</td>
                                    <td class="p-4">
                                        <span class="border-2 border-black rounded-full px-2 py-0.5 text-[10px] font-black uppercase {{ $booking->status->badgeClasses() }}">
                                            {{ $booking->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

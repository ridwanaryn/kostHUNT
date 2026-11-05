<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Tenant Dashboard</h2>
            <a href="{{ route('rooms.index') }}"
               class="bg-yellow border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                Find a room
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="bg-cyan border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                    <p class="text-xs font-black uppercase text-neutral-700">Unread notifications</p>
                    <p class="font-display text-3xl font-black mt-1">{{ $unreadNotifications }}</p>
                    <a href="{{ route('notifications.index') }}" class="text-xs font-bold underline">View notifications</a>
                </div>
                <div class="bg-pink border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                    <p class="text-xs font-black uppercase text-neutral-700">Saved rooms</p>
                    <p class="font-display text-3xl font-black mt-1">{{ $favoritesCount }}</p>
                    <a href="{{ route('tenant.favorites.index') }}" class="text-xs font-bold underline">View favorites</a>
                </div>
                <div class="bg-yellow border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                    <p class="text-xs font-black uppercase text-neutral-700">Unpaid invoices</p>
                    <p class="font-display text-3xl font-black mt-1">{{ $unpaidInvoices->count() }}</p>
                    <a href="{{ route('tenant.invoices.index') }}" class="text-xs font-bold underline">View invoices</a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display text-lg font-black uppercase">My bookings</h3>
                        <a href="{{ route('tenant.bookings.index') }}" class="text-sm font-bold underline">All</a>
                    </div>
                    @if ($bookings->isEmpty())
                        <p class="mt-3 text-sm text-neutral-600">No bookings yet.</p>
                    @else
                        <div class="mt-3 space-y-3">
                            @foreach ($bookings as $booking)
                                <a href="{{ route('tenant.bookings.show', $booking) }}"
                                   class="block border-2 border-black rounded-xl p-3 bg-canvas hover:bg-yellow transition">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold">{{ $booking->room->name }} &middot; {{ $booking->room->building->name }}</p>
                                        <span class="border-2 border-black rounded-full px-2 py-0.5 text-[10px] font-black uppercase {{ $booking->status->badgeClasses() }}">{{ $booking->status->label() }}</span>
                                    </div>
                                    <p class="text-xs font-bold text-neutral-600 mt-1">{{ $booking->start_date->format('d M Y') }} &rarr; {{ $booking->end_date->format('d M Y') }}</p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display text-lg font-black uppercase">Unpaid invoices</h3>
                        <a href="{{ route('tenant.invoices.index') }}" class="text-sm font-bold underline">All</a>
                    </div>
                    @if ($unpaidInvoices->isEmpty())
                        <p class="mt-3 text-sm text-neutral-600">You're all paid up.</p>
                    @else
                        <div class="mt-3 space-y-3">
                            @foreach ($unpaidInvoices as $invoice)
                                <a href="{{ route('tenant.invoices.show', $invoice) }}"
                                   class="block border-2 border-black rounded-xl p-3 bg-canvas hover:bg-yellow transition">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold">{{ $invoice->invoice_number }}</p>
                                        @if ($invoice->isOverdue())
                                            <span class="border-2 border-black rounded-full px-2 py-0.5 text-[10px] font-black uppercase bg-coral text-white">Overdue</span>
                                        @else
                                            <span class="border-2 border-black rounded-full px-2 py-0.5 text-[10px] font-black uppercase {{ $invoice->status->badgeClasses() }}">{{ $invoice->status->label() }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs font-bold text-neutral-600 mt-1">{{ \App\Support\Money::format($invoice->amount) }} &middot; due {{ $invoice->due_date->format('d M Y') }}</p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                <h3 class="font-display text-lg font-black uppercase">Quick links</h3>
                <div class="mt-3 flex flex-wrap gap-3 text-sm font-bold">
                    <a href="{{ route('tenant.bookings.index') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">Bookings</a>
                    <a href="{{ route('tenant.invoices.index') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">Invoices</a>
                    <a href="{{ route('tenant.favorites.index') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">Favorites</a>
                    <a href="{{ route('tenant.maintenance-requests.index') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">Requests</a>
                    <a href="{{ route('chat.index') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">Messages</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">{{ $booking->room->name }}</h2>
                    <span class="border-2 border-black rounded-full px-2 py-0.5 text-xs font-black uppercase {{ $booking->status->badgeClasses() }}">
                        {{ $booking->status->label() }}
                    </span>
                </div>
                <p class="text-sm text-neutral-600">
                    <a href="{{ route('buildings.show', $booking->room->building) }}" class="underline">{{ $booking->room->building->name }}</a>
                    &middot; {{ $booking->room->building->city }}
                </p>
            </div>
            <a href="{{ route('tenant.bookings.index') }}" class="font-bold underline underline-offset-4">Back to bookings</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-black uppercase">Booking details</h3>
                    <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="font-black uppercase text-neutral-600 text-xs">Start date</dt>
                            <dd class="font-bold">{{ $booking->start_date->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="font-black uppercase text-neutral-600 text-xs">End date</dt>
                            <dd class="font-bold">{{ $booking->end_date->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="font-black uppercase text-neutral-600 text-xs">Period</dt>
                            <dd class="font-bold">{{ $booking->period_type->label() }} &times; {{ $booking->duration }}</dd>
                        </div>
                        <div>
                            <dt class="font-black uppercase text-neutral-600 text-xs">Rent per period</dt>
                            <dd class="font-bold">{{ \App\Support\Money::format($booking->pricePerPeriod()) }}</dd>
                        </div>
                        <div>
                            <dt class="font-black uppercase text-neutral-600 text-xs">Total rent</dt>
                            <dd class="font-bold">{{ \App\Support\Money::format($booking->rent_amount) }}</dd>
                        </div>
                        <div>
                            <dt class="font-black uppercase text-neutral-600 text-xs">Room</dt>
                            <dd class="font-bold"><a href="{{ route('rooms.show', $booking->room) }}" class="underline">View room listing</a></dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-yellow border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-black uppercase">At a glance</h3>
                    <p class="mt-3 text-xs font-black uppercase text-neutral-700">Monthly price</p>
                    <p class="font-display text-3xl font-black">{{ \App\Support\Money::format($booking->room->price_monthly) }}</p>
                    <p class="mt-3 text-sm font-bold">{{ $booking->room->building->street }}, {{ $booking->room->building->city }}</p>
                </div>
            </div>

            @if ($booking->status === \App\Enums\BookingStatus::TerminationRequested)
                <div class="bg-coral text-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-black uppercase">Termination requested</h3>
                    <p class="mt-2 font-bold">Requested move-out: {{ $booking->requested_move_out_date?->format('d M Y') }}</p>
                    @if ($booking->termination_reason)
                        <p class="mt-1 text-sm">Reason: {{ $booking->termination_reason }}</p>
                    @endif
                    <p class="mt-2 text-sm font-medium">Waiting for the owner to respond.</p>
                </div>
            @endif

            @if ($booking->status === \App\Enums\BookingStatus::Active || $booking->status === \App\Enums\BookingStatus::Upcoming)
                <div class="grid gap-6 md:grid-cols-2">
                    @can('renew', $booking)
                        <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                            <h3 class="font-display text-lg font-black uppercase">Renew this booking</h3>
                            <p class="text-sm text-neutral-600 mt-1">This booking ends {{ $booking->end_date->format('d M Y') }}. Renew for another term.</p>
                            <form method="POST" action="{{ route('tenant.bookings.renew', $booking) }}" class="mt-4 space-y-3">
                                @csrf
                                @error('renew')
                                    <p class="text-sm font-bold text-coral">{{ $message }}</p>
                                @enderror
                                <div>
                                    <label for="renew_period_type" class="block text-xs font-black uppercase text-neutral-600 mb-1">Period</label>
                                    <select id="renew_period_type" name="period_type" class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                                        <option value="monthly">Monthly</option>
                                        @if ($booking->room->price_yearly)
                                            <option value="yearly">Yearly</option>
                                        @endif
                                    </select>
                                    @error('period_type') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="renew_duration" class="block text-xs font-black uppercase text-neutral-600 mb-1">Duration</label>
                                    <input id="renew_duration" name="duration" type="number" min="1" max="{{ (int) config('kosthunt.booking.max_duration') }}" value="{{ old('duration', 1) }}"
                                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                                    @error('duration') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                                </div>
                                <button type="submit" class="w-full bg-yellow border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                                    Renew booking
                                </button>
                            </form>
                        </div>
                    @endcan

                    @can('requestTermination', $booking)
                        <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                            <h3 class="font-display text-lg font-black uppercase">End my stay early</h3>
                            <p class="text-sm text-neutral-600 mt-1">Request a move-out date. The owner will review it.</p>
                            <form method="POST" action="{{ route('tenant.bookings.termination', $booking) }}" class="mt-4 space-y-3">
                                @csrf
                                <div>
                                    <label for="requested_move_out_date" class="block text-xs font-black uppercase text-neutral-600 mb-1">Move-out date</label>
                                    <input id="requested_move_out_date" name="requested_move_out_date" type="date"
                                           min="{{ now()->toDateString() }}"
                                           max="{{ $booking->end_date->copy()->subDay()->toDateString() }}"
                                           value="{{ old('requested_move_out_date') }}"
                                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                                    @error('requested_move_out_date') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="termination_reason" class="block text-xs font-black uppercase text-neutral-600 mb-1">Reason (optional)</label>
                                    <textarea id="termination_reason" name="reason" rows="2"
                                              class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">{{ old('reason') }}</textarea>
                                    @error('reason') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                                </div>
                                <button type="submit" class="w-full bg-coral text-white border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                                    Request termination
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            @endif

            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                <h3 class="font-display text-lg font-black uppercase">Invoices</h3>
                @if ($booking->invoices->isEmpty())
                    <p class="mt-2 text-neutral-600">No invoices yet.</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left font-black uppercase text-xs text-neutral-600 border-b-2 border-black">
                                    <th class="py-2 pr-4">Invoice</th>
                                    <th class="py-2 pr-4">Period</th>
                                    <th class="py-2 pr-4">Due</th>
                                    <th class="py-2 pr-4">Amount</th>
                                    <th class="py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->invoices as $invoice)
                                    <tr class="border-b border-neutral-200">
                                        <td class="py-2 pr-4 font-bold">{{ $invoice->invoice_number }}</td>
                                        <td class="py-2 pr-4">{{ $invoice->period_start->format('d M Y') }} &rarr; {{ $invoice->period_end->format('d M Y') }}</td>
                                        <td class="py-2 pr-4">{{ $invoice->due_date->format('d M Y') }}</td>
                                        <td class="py-2 pr-4 font-bold">{{ \App\Support\Money::format($invoice->amount) }}</td>
                                        <td class="py-2">
                                            <span class="border-2 border-black rounded-full px-2 py-0.5 text-[10px] font-black uppercase {{ $invoice->status->badgeClasses() }}">
                                                {{ $invoice->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

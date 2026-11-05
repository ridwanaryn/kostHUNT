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
                <p class="text-sm text-neutral-600">{{ $booking->room->building->name }} &middot; {{ $booking->tenant->name }}</p>
            </div>
            <a href="{{ route('owner.bookings.index') }}" class="font-bold underline underline-offset-4">Back to bookings</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-black uppercase">Booking details</h3>
                    <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="font-black uppercase text-neutral-600 text-xs">Tenant</dt>
                            <dd class="font-bold">{{ $booking->tenant->name }} ({{ $booking->tenant->email }})</dd>
                        </div>
                        <div>
                            <dt class="font-black uppercase text-neutral-600 text-xs">Room</dt>
                            <dd class="font-bold">{{ $booking->room->name }}</dd>
                        </div>
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
                            <dt class="font-black uppercase text-neutral-600 text-xs">Total rent</dt>
                            <dd class="font-bold">{{ \App\Support\Money::format($booking->rent_amount) }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-yellow border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-black uppercase">Income</h3>
                    <p class="mt-3 text-xs font-black uppercase text-neutral-700">Rent per {{ $booking->period_type->label() }}</p>
                    <p class="font-display text-3xl font-black">{{ \App\Support\Money::format($booking->pricePerPeriod()) }}</p>
                </div>
            </div>

            @if ($booking->status === \App\Enums\BookingStatus::TerminationRequested)
                <div class="bg-coral text-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-black uppercase">Termination request</h3>
                    <p class="mt-2 font-bold">Move-out requested: {{ $booking->requested_move_out_date?->format('d M Y') }}</p>
                    @if ($booking->termination_reason)
                        <p class="mt-1 text-sm">Reason: {{ $booking->termination_reason }}</p>
                    @endif

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <form method="POST" action="{{ route('owner.bookings.termination.approve', $booking) }}"
                              class="bg-white text-onyx border-2 border-black rounded-xl p-4">
                            @csrf
                            <label for="approve_note" class="block text-xs font-black uppercase mb-1">Note (optional)</label>
                            <textarea id="approve_note" name="note" rows="2"
                                      class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">{{ old('note') }}</textarea>
                            @error('note') <p class="mt-1 text-xs font-bold text-coral">{{ $message }}</p> @enderror
                            <button type="submit" class="mt-3 w-full bg-mint border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000]">
                                Approve &amp; end booking
                            </button>
                        </form>

                        <form method="POST" action="{{ route('owner.bookings.termination.reject', $booking) }}"
                              class="bg-white text-onyx border-2 border-black rounded-xl p-4">
                            @csrf
                            <label for="reject_note" class="block text-xs font-black uppercase mb-1">Note (optional)</label>
                            <textarea id="reject_note" name="note" rows="2"
                                      class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white"></textarea>
                            <button type="submit" class="mt-3 w-full bg-lilac border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000]">
                                Reject request
                            </button>
                        </form>
                    </div>
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

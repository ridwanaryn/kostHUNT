<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">{{ $invoice->invoice_number }}</h2>
                <p class="text-sm text-neutral-600">{{ $invoice->booking->room->name }} &middot; {{ $invoice->booking->room->building->name }}</p>
            </div>
            <a href="{{ route('tenant.invoices.index') }}" class="font-bold underline underline-offset-4">Back to invoices</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase text-neutral-600">Amount due</p>
                        <p class="font-display text-4xl font-black">{{ \App\Support\Money::format($invoice->amount) }}</p>
                    </div>
                    @if ($invoice->isOverdue())
                        <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-black uppercase bg-coral text-white">Overdue</span>
                    @else
                        <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-black uppercase {{ $invoice->status->badgeClasses() }}">{{ $invoice->status->label() }}</span>
                    @endif
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-black uppercase text-neutral-600 text-xs">Billing period</dt>
                        <dd class="font-bold">{{ $invoice->period_start->format('d M Y') }} &rarr; {{ $invoice->period_end->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="font-black uppercase text-neutral-600 text-xs">Due date</dt>
                        <dd class="font-bold">{{ $invoice->due_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="font-black uppercase text-neutral-600 text-xs">Room</dt>
                        <dd class="font-bold">{{ $invoice->booking->room->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-black uppercase text-neutral-600 text-xs">Building</dt>
                        <dd class="font-bold">{{ $invoice->booking->room->building->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-black uppercase text-neutral-600 text-xs">Paid at</dt>
                        <dd class="font-bold">{{ $invoice->paid_at ? $invoice->paid_at->format('d M Y H:i') : 'Not paid yet' }}</dd>
                    </div>
                    <div>
                        <dt class="font-black uppercase text-neutral-600 text-xs">Booking</dt>
                        <dd class="font-bold"><a href="{{ route('tenant.bookings.show', $invoice->booking) }}" class="underline">View booking</a></dd>
                    </div>
                </dl>

                <div class="border-2 border-black rounded-xl bg-canvas p-4 text-sm font-medium">
                    Payments are handled offline. Once the owner receives your payment they will mark this invoice as paid.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

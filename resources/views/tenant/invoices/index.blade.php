<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">My Invoices</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($invoices->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No invoices yet</p>
                    <p class="text-neutral-600 mt-2">Invoices appear here once you have a booking.</p>
                </div>
            @else
                <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left font-black uppercase text-xs text-neutral-600 border-b-2 border-black">
                                <th class="p-4">Invoice</th>
                                <th class="p-4">Room</th>
                                <th class="p-4">Period</th>
                                <th class="p-4">Due</th>
                                <th class="p-4">Amount</th>
                                <th class="p-4">Status</th>
                                <th class="p-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr class="border-b border-neutral-200">
                                    <td class="p-4 font-bold">{{ $invoice->invoice_number }}</td>
                                    <td class="p-4">{{ $invoice->booking->room->name }}<br><span class="text-neutral-600">{{ $invoice->booking->room->building->name }}</span></td>
                                    <td class="p-4">{{ $invoice->period_start->format('d M Y') }} &rarr; {{ $invoice->period_end->format('d M Y') }}</td>
                                    <td class="p-4">{{ $invoice->due_date->format('d M Y') }}</td>
                                    <td class="p-4 font-bold">{{ \App\Support\Money::format($invoice->amount) }}</td>
                                    <td class="p-4">
                                        @if ($invoice->isOverdue())
                                            <span class="border-2 border-black rounded-full px-2 py-0.5 text-[10px] font-black uppercase bg-coral text-white">Overdue</span>
                                        @else
                                            <span class="border-2 border-black rounded-full px-2 py-0.5 text-[10px] font-black uppercase {{ $invoice->status->badgeClasses() }}">{{ $invoice->status->label() }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('tenant.invoices.show', $invoice) }}" class="font-bold underline">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>{{ $invoices->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

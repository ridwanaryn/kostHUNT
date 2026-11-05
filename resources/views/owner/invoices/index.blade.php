<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Invoices</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('owner.invoices.index') }}"
                  class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label for="building_id" class="block text-xs font-black uppercase text-neutral-600 mb-1">Building</label>
                    <select id="building_id" name="building_id" class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                        <option value="">All</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->id }}" @selected((int) ($filters['building_id'] ?? 0) === $building->id)>{{ $building->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="room_id" class="block text-xs font-black uppercase text-neutral-600 mb-1">Room</label>
                    <select id="room_id" name="room_id" class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                        <option value="">All</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" @selected((int) ($filters['room_id'] ?? 0) === $room->id)>{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tenant_id" class="block text-xs font-black uppercase text-neutral-600 mb-1">Tenant</label>
                    <select id="tenant_id" name="tenant_id" class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                        <option value="">All</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}" @selected((int) ($filters['tenant_id'] ?? 0) === $tenant->id)>{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-xs font-black uppercase text-neutral-600 mb-1">Status</label>
                    <select id="status" name="status" class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                        <option value="">All</option>
                        @foreach (\App\Enums\InvoiceStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="bg-cyan border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000]">Filter</button>
                    <a href="{{ route('owner.invoices.index') }}" class="font-bold underline">Reset</a>
                </div>
            </form>

            @if ($invoices->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No invoices found</p>
                </div>
            @else
                <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left font-black uppercase text-xs text-neutral-600 border-b-2 border-black">
                                <th class="p-4">Invoice</th>
                                <th class="p-4">Tenant</th>
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
                                    <td class="p-4">{{ $invoice->booking->tenant->name }}</td>
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
                                        @if ($invoice->status === \App\Enums\InvoiceStatus::Unpaid)
                                            <form method="POST" action="{{ route('owner.invoices.mark-paid', $invoice) }}">
                                                @csrf
                                                <button type="submit" class="bg-mint border-2 border-black rounded-lg px-3 py-1 text-xs font-black uppercase shadow-[2px_2px_0px_#000] hover:-translate-y-0.5 transition">
                                                    Mark paid
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-bold text-neutral-500">{{ $invoice->paid_at?->format('d M Y') }}</span>
                                        @endif
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

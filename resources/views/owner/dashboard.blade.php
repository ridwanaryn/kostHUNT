<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Owner Dashboard</h2>
            <a href="{{ route('owner.buildings.create') }}"
               class="bg-yellow border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                + New building
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-yellow border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                    <p class="text-xs font-black uppercase text-neutral-700">Income this month</p>
                    <p class="font-display text-3xl font-black mt-1">{{ \App\Support\Money::format($summary['income_this_month']) }}</p>
                </div>
                <div class="bg-mint border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                    <p class="text-xs font-black uppercase text-neutral-700">Occupancy</p>
                    <p class="font-display text-3xl font-black mt-1">{{ $summary['occupancy_rate'] }}%</p>
                    <p class="text-xs font-bold text-neutral-700">{{ $summary['occupied_count'] }} / {{ $summary['rooms_count'] }} rooms</p>
                </div>
                <div class="bg-cyan border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                    <p class="text-xs font-black uppercase text-neutral-700">Buildings</p>
                    <p class="font-display text-3xl font-black mt-1">{{ $summary['buildings_count'] }}</p>
                    <p class="text-xs font-bold text-neutral-700">{{ $summary['rooms_count'] }} rooms total</p>
                </div>
                <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                    <p class="text-xs font-black uppercase text-neutral-600">Unpaid invoices</p>
                    <p class="font-display text-3xl font-black mt-1">{{ $summary['unpaid_invoices'] }}</p>
                    <p class="text-xs font-bold text-coral">{{ $summary['overdue_invoices'] }} overdue</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <a href="{{ route('owner.bookings.index', ['status' => 'termination_requested']) }}"
                   class="bg-coral text-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 hover:-translate-y-0.5 transition">
                    <p class="text-xs font-black uppercase">Pending termination requests</p>
                    <p class="font-display text-3xl font-black mt-1">{{ $summary['pending_terminations'] }}</p>
                </a>
                <a href="{{ route('owner.maintenance-requests.index') }}"
                   class="bg-lilac border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 hover:-translate-y-0.5 transition">
                    <p class="text-xs font-black uppercase">Open maintenance requests</p>
                    <p class="font-display text-3xl font-black mt-1">{{ $summary['open_maintenance'] }}</p>
                </a>
            </div>

            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                <h3 class="font-display text-lg font-black uppercase">Quick links</h3>
                <div class="mt-3 flex flex-wrap gap-3 text-sm font-bold">
                    <a href="{{ route('owner.buildings.index') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">My buildings</a>
                    <a href="{{ route('owner.bookings.index') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">Bookings</a>
                    <a href="{{ route('owner.invoices.index') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">Invoices</a>
                    <a href="{{ route('owner.reports.income') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">Income report</a>
                    <a href="{{ route('owner.maintenance-requests.index') }}" class="border-2 border-black rounded-lg px-3 py-1.5 bg-canvas hover:bg-yellow transition">Maintenance</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

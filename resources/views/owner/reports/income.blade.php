<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Income Report</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('owner.reports.income') }}"
                  class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label for="from" class="block text-xs font-black uppercase text-neutral-600 mb-1">From</label>
                    <input id="from" name="from" type="date" value="{{ $filters['from'] ?? '' }}"
                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                </div>
                <div>
                    <label for="to" class="block text-xs font-black uppercase text-neutral-600 mb-1">To</label>
                    <input id="to" name="to" type="date" value="{{ $filters['to'] ?? '' }}"
                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                </div>
                <div>
                    <label for="group_by" class="block text-xs font-black uppercase text-neutral-600 mb-1">Group by</label>
                    <select id="group_by" name="group_by" class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                        <option value="month" @selected(($filters['group_by'] ?? 'month') === 'month')>Month</option>
                        <option value="year" @selected(($filters['group_by'] ?? '') === 'year')>Year</option>
                    </select>
                </div>
                <div>
                    <label for="building_id" class="block text-xs font-black uppercase text-neutral-600 mb-1">Building</label>
                    <select id="building_id" name="building_id" class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                        <option value="">All</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->id }}" @selected((int) ($filters['building_id'] ?? 0) === $building->id)>{{ $building->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="bg-cyan border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000]">Apply</button>
                    <a href="{{ route('owner.reports.income') }}" class="font-bold underline">Reset</a>
                </div>
            </form>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="bg-yellow border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                    <p class="text-xs font-black uppercase text-neutral-700">Total income</p>
                    <p class="font-display text-3xl font-black mt-1">{{ \App\Support\Money::format($report['total']) }}</p>
                </div>
                <div class="bg-mint border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                    <p class="text-xs font-black uppercase text-neutral-700">Paid invoices</p>
                    <p class="font-display text-3xl font-black mt-1">{{ $report['count'] }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-black uppercase">By {{ $report['group_by'] }}</h3>
                    @if ($report['by_group']->isEmpty())
                        <p class="mt-3 text-sm text-neutral-600">No paid invoices in this range.</p>
                    @else
                        <table class="mt-3 w-full text-sm">
                            <thead>
                                <tr class="text-left font-black uppercase text-xs text-neutral-600 border-b-2 border-black">
                                    <th class="py-2">Period</th>
                                    <th class="py-2">Invoices</th>
                                    <th class="py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['by_group'] as $row)
                                    <tr class="border-b border-neutral-200">
                                        <td class="py-2 font-bold">{{ $row['period'] }}</td>
                                        <td class="py-2">{{ $row['count'] }}</td>
                                        <td class="py-2 text-right font-bold">{{ \App\Support\Money::format($row['total']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
                    <h3 class="font-display text-lg font-black uppercase">By building</h3>
                    @if ($report['by_building']->isEmpty())
                        <p class="mt-3 text-sm text-neutral-600">No paid invoices in this range.</p>
                    @else
                        <table class="mt-3 w-full text-sm">
                            <thead>
                                <tr class="text-left font-black uppercase text-xs text-neutral-600 border-b-2 border-black">
                                    <th class="py-2">Building</th>
                                    <th class="py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['by_building'] as $row)
                                    <tr class="border-b border-neutral-200">
                                        <td class="py-2 font-bold">{{ $row['building'] }}</td>
                                        <td class="py-2 text-right font-bold">{{ \App\Support\Money::format($row['total']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

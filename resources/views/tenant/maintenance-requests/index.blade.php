<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Maintenance Requests</h2>
            <a href="{{ route('tenant.maintenance-requests.create') }}"
               class="bg-yellow border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                + New request
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if ($requests->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No requests yet</p>
                    <p class="text-neutral-600 mt-2">Submit a maintenance request when something needs fixing.</p>
                </div>
            @else
                <div class="grid gap-3">
                    @foreach ($requests as $maintenanceRequest)
                        <a href="{{ route('tenant.maintenance-requests.show', $maintenanceRequest) }}"
                           class="block bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 hover:-translate-y-0.5 transition">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-display text-lg font-black uppercase">{{ $maintenanceRequest->title }}</h3>
                                    <p class="text-sm text-neutral-600">{{ $maintenanceRequest->room->name }} &middot; {{ $maintenanceRequest->room->building->name }}</p>
                                </div>
                                <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-black uppercase {{ $maintenanceRequest->status->badgeClasses() }}">
                                    {{ $maintenanceRequest->status->label() }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-neutral-700 line-clamp-2">{{ $maintenanceRequest->description }}</p>
                            <p class="mt-2 text-xs font-bold text-neutral-500">Submitted {{ $maintenanceRequest->created_at->format('d M Y') }}</p>
                        </a>
                    @endforeach
                </div>

                <div>{{ $requests->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

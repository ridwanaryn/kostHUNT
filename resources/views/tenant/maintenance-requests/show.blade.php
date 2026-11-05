<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">{{ $maintenanceRequest->title }}</h2>
                    <span class="border-2 border-black rounded-full px-2 py-0.5 text-xs font-black uppercase {{ $maintenanceRequest->status->badgeClasses() }}">
                        {{ $maintenanceRequest->status->label() }}
                    </span>
                </div>
                <p class="text-sm text-neutral-600">{{ $maintenanceRequest->room->name }} &middot; {{ $maintenanceRequest->room->building->name }}</p>
            </div>
            <a href="{{ route('tenant.maintenance-requests.index') }}" class="font-bold underline underline-offset-4">Back to requests</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6 space-y-5">
                <div>
                    <h3 class="font-black uppercase text-xs text-neutral-600">Description</h3>
                    <p class="mt-1 text-neutral-800 whitespace-pre-line">{{ $maintenanceRequest->description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-black uppercase text-xs text-neutral-600">Submitted</dt>
                        <dd class="font-bold">{{ $maintenanceRequest->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="font-black uppercase text-xs text-neutral-600">Resolved at</dt>
                        <dd class="font-bold">{{ $maintenanceRequest->resolved_at?->format('d M Y H:i') ?? 'Not resolved' }}</dd>
                    </div>
                </div>

                <div>
                    <h3 class="font-black uppercase text-xs text-neutral-600">Owner note</h3>
                    <p class="mt-1 text-neutral-800 whitespace-pre-line">{{ $maintenanceRequest->owner_note ?: 'No note from the owner yet.' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

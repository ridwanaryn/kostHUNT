<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Maintenance Requests</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('owner.maintenance-requests.index') }}"
                  class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
                    <label for="status" class="block text-xs font-black uppercase text-neutral-600 mb-1">Status</label>
                    <select id="status" name="status" class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                        <option value="">All</option>
                        @foreach (\App\Enums\MaintenanceStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="bg-cyan border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000]">Filter</button>
                    <a href="{{ route('owner.maintenance-requests.index') }}" class="font-bold underline">Reset</a>
                </div>
            </form>

            @if ($requests->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No maintenance requests</p>
                </div>
            @else
                <div class="grid gap-4">
                    @foreach ($requests as $maintenanceRequest)
                        <div class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-display text-lg font-black uppercase">{{ $maintenanceRequest->title }}</h3>
                                    <p class="text-sm text-neutral-600">
                                        {{ $maintenanceRequest->room->name }} &middot; {{ $maintenanceRequest->room->building->name }}
                                        &middot; {{ $maintenanceRequest->tenant->name }}
                                    </p>
                                </div>
                                <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-black uppercase {{ $maintenanceRequest->status->badgeClasses() }}">
                                    {{ $maintenanceRequest->status->label() }}
                                </span>
                            </div>

                            <p class="mt-2 text-sm text-neutral-800 whitespace-pre-line">{{ $maintenanceRequest->description }}</p>

                            <form method="POST" action="{{ route('owner.maintenance-requests.update', $maintenanceRequest) }}"
                                  class="mt-4 grid gap-3 sm:grid-cols-[160px_1fr_auto] sm:items-end">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="status-{{ $maintenanceRequest->id }}" class="block text-xs font-black uppercase text-neutral-600 mb-1">Status</label>
                                    <select id="status-{{ $maintenanceRequest->id }}" name="status"
                                            class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                                        @foreach (\App\Enums\MaintenanceStatus::cases() as $status)
                                            <option value="{{ $status->value }}" @selected($maintenanceRequest->status === $status)>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="note-{{ $maintenanceRequest->id }}" class="block text-xs font-black uppercase text-neutral-600 mb-1">Owner note</label>
                                    <input id="note-{{ $maintenanceRequest->id }}" name="owner_note" type="text" maxlength="1000"
                                           value="{{ $maintenanceRequest->owner_note }}"
                                           class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white">
                                </div>
                                <button type="submit" class="bg-mint border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                                    Update
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div>{{ $requests->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

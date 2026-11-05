<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">New Room</h2>
            <p class="text-sm text-gray-600">{{ $building->name }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-black rounded-2xl shadow-[6px_6px_0px_#000] p-6">
                @include('owner.rooms._form', [
                    'room' => null,
                    'building' => $building,
                    'action' => route('owner.rooms.store', $building),
                    'method' => 'POST',
                    'submitLabel' => 'Create room',
                    'cancelUrl' => route('owner.buildings.show', $building),
                ])
            </div>
        </div>
    </div>
</x-app-layout>

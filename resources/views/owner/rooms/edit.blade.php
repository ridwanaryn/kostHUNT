<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Edit {{ $room->name }}</h2>
            <p class="text-sm text-gray-600">{{ $room->building->name }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border-2 border-black rounded-2xl shadow-[6px_6px_0px_#000] p-6">
                @include('owner.rooms._form', [
                    'room' => $room,
                    'building' => $room->building,
                    'action' => route('owner.rooms.update', $room),
                    'method' => 'PATCH',
                    'submitLabel' => 'Save changes',
                    'cancelUrl' => route('owner.rooms.show', $room),
                ])
            </div>

            @include('owner.partials.photo-manager', [
                'photos' => $room->photos,
                'uploadRoute' => route('owner.rooms.photos.store', $room),
            ])
        </div>
    </div>
</x-app-layout>

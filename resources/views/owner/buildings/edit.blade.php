<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Edit {{ $building->name }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border-2 border-black rounded-2xl shadow-[6px_6px_0px_#000] p-6">
                @include('owner.buildings._form', [
                    'building' => $building,
                    'action' => route('owner.buildings.update', $building),
                    'method' => 'PATCH',
                    'submitLabel' => 'Save changes',
                    'cancelUrl' => route('owner.buildings.show', $building),
                ])
            </div>

            @include('owner.partials.photo-manager', [
                'photos' => $building->photos,
                'uploadRoute' => route('owner.buildings.photos.store', $building),
            ])
        </div>
    </div>
</x-app-layout>

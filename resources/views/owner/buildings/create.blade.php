<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">New Building</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-black rounded-2xl shadow-[6px_6px_0px_#000] p-6">
                @include('owner.buildings._form', [
                    'building' => null,
                    'action' => route('owner.buildings.store'),
                    'method' => 'POST',
                    'submitLabel' => 'Create building',
                    'cancelUrl' => route('owner.buildings.index'),
                ])
            </div>
        </div>
    </div>
</x-app-layout>

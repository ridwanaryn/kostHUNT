<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Favorites</h2>
            <a href="{{ route('rooms.index') }}"
               class="bg-yellow border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                Browse rooms
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($rooms->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No favorites yet</p>
                    <p class="text-neutral-600 mt-2">Tap the heart on any room to save it here.</p>
                    <a href="{{ route('rooms.index') }}" class="inline-block mt-4 bg-cyan border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000]">Find rooms</a>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($rooms as $room)
                        <div>
                            <x-room-card :room="$room" />
                            <form method="POST" action="{{ route('tenant.favorites.destroy', $room) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-coral text-white border-2 border-black rounded-lg px-3 py-2 text-sm font-bold uppercase shadow-[2px_2px_0px_#000] hover:-translate-y-0.5 transition">
                                    Remove from favorites
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div>{{ $rooms->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

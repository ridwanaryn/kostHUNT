<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Messages</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if ($conversations->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No conversations yet</p>
                    <p class="text-neutral-600 mt-2">Open a room and tap "Chat with owner" to start talking.</p>
                    <a href="{{ route('rooms.index') }}" class="inline-block mt-4 bg-cyan border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000]">Browse rooms</a>
                </div>
            @else
                <div class="grid gap-3">
                    @foreach ($conversations as $conversation)
                        @php $other = $conversation->otherParticipant(auth()->user()); @endphp
                        <a href="{{ route('chat.show', $conversation) }}"
                           class="flex items-center justify-between gap-4 bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-4 hover:-translate-y-0.5 transition">
                            <div class="min-w-0">
                                <p class="font-display font-black uppercase">{{ $other->name }}</p>
                                @if ($conversation->room)
                                    <p class="text-xs font-bold text-neutral-600">{{ $conversation->room->name }} &middot; {{ $conversation->room->building?->name }}</p>
                                @endif
                                <p class="text-sm text-neutral-700 truncate mt-1">
                                    {{ $conversation->latestMessage?->body ?? 'No messages yet' }}
                                </p>
                            </div>
                            <div class="shrink-0 flex flex-col items-end gap-1">
                                @if (($conversation->unread_count ?? 0) > 0)
                                    <span class="bg-coral text-white border-2 border-black rounded-full px-2 py-0.5 text-xs font-black">{{ $conversation->unread_count }}</span>
                                @endif
                                <span class="text-[10px] font-bold text-neutral-500">{{ $conversation->updated_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div>{{ $conversations->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                @php $other = $conversation->otherParticipant(auth()->user()); @endphp
                <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">{{ $other->name }}</h2>
                @if ($conversation->room)
                    <p class="text-sm text-neutral-600">
                        <a href="{{ route('rooms.show', $conversation->room) }}" class="underline">{{ $conversation->room->name }}</a>
                        &middot; {{ $conversation->room->building?->name }}
                    </p>
                @endif
            </div>
            <a href="{{ route('chat.index') }}" class="font-bold underline underline-offset-4">Back to inbox</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div x-data
                 x-init="setInterval(async () => {
                     try {
                         const response = await fetch('{{ route('chat.messages.index', $conversation) }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                         if (response.ok) {
                             const list = document.getElementById('message-list');
                             list.innerHTML = await response.text();
                             list.scrollTop = list.scrollHeight;
                         }
                     } catch (error) {}
                 }, 15000);">
                <div id="message-list"
                     class="bg-canvas border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-4 space-y-3 max-h-[60vh] overflow-y-auto">
                    @include('chat.partials.messages', ['messages' => $conversation->messages])
                </div>
            </div>

            <form method="POST" action="{{ route('chat.messages.store', $conversation) }}"
                  class="bg-white border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-4 space-y-2">
                @csrf
                <label for="body" class="sr-only">Message</label>
                <textarea id="body" name="body" rows="2" required maxlength="2000"
                          class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white"
                          placeholder="Write a message...">{{ old('body') }}</textarea>
                @error('body') <p class="text-xs font-bold text-coral">{{ $message }}</p> @enderror
                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-cyan border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@forelse ($messages as $message)
    @php $mine = $message->sender_id === auth()->id(); @endphp

    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
        <div class="max-w-[80%] border-2 border-black rounded-2xl px-3 py-2 shadow-[2px_2px_0px_#000] {{ $mine ? 'bg-cyan' : 'bg-white' }}">
            @unless ($mine)
                <p class="text-[10px] font-black uppercase text-neutral-600">{{ $message->sender->name }}</p>
            @endunless
            <p class="text-sm whitespace-pre-line">{{ $message->body }}</p>
            <p class="text-[10px] font-bold text-neutral-600 mt-1">{{ $message->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>
@empty
    <p class="text-center text-sm font-bold text-neutral-500 py-6">No messages yet. Say hello!</p>
@endforelse

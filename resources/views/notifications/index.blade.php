<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-2xl font-black uppercase tracking-tight text-onyx">Notifications</h2>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="bg-white border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                    Mark all as read
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-3">
            @if ($notifications->isEmpty())
                <div class="bg-white border-[3px] border-black rounded-2xl p-10 text-center shadow-[4px_4px_0px_#000]">
                    <p class="font-display text-xl font-black uppercase">No notifications</p>
                    <p class="text-neutral-600 mt-2">Booking, payment, and maintenance updates will appear here.</p>
                </div>
            @else
                @foreach ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $unread = $notification->read_at === null;
                    @endphp
                    <div class="border-[3px] border-black rounded-2xl shadow-[4px_4px_0px_#000] p-4 flex items-start justify-between gap-4 {{ $unread ? 'bg-yellow' : 'bg-white' }}">
                        <div class="min-w-0">
                            <p class="font-bold">{{ $data['message'] ?? 'Notification' }}</p>
                            <p class="text-xs font-bold text-neutral-600 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            @if (! empty($data['url']))
                                <a href="{{ $data['url'] }}" class="inline-block mt-2 text-sm font-bold underline underline-offset-4">Open</a>
                            @endif
                        </div>
                        @if ($unread)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="shrink-0 bg-white border-2 border-black rounded-lg px-3 py-1 text-xs font-black uppercase shadow-[2px_2px_0px_#000]">
                                    Mark read
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach

                <div class="mt-4">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()?->id;

        $conversations = Conversation::query()
            ->where('tenant_id', $userId)
            ->orWhere('owner_id', $userId)
            ->with(['tenant', 'owner', 'room.building', 'latestMessage'])
            ->withCount([
                'messages as unread_count' => fn ($query) => $query
                    ->whereNull('read_at')
                    ->where('sender_id', '!=', $userId),
            ])
            ->latest('updated_at')
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('chat.index', ['conversations' => $conversations]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $this->markRead($conversation, $request->user()?->id);
        $conversation->load(['messages.sender', 'tenant', 'owner', 'room.building']);

        return view('chat.show', ['conversation' => $conversation]);
    }

    public function messages(Request $request, Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $this->markRead($conversation, $request->user()?->id);
        $conversation->load('messages.sender');

        return view('chat.partials.messages', ['messages' => $conversation->messages]);
    }

    public function start(Request $request, Room $room): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $room->loadMissing('building');

        if ($room->building->owner_id === $user->id) {
            abort(403);
        }

        $conversation = Conversation::query()->firstOrCreate([
            'tenant_id' => $user->id,
            'owner_id' => $room->building->owner_id,
            'room_id' => $room->id,
        ]);

        return redirect()->route('chat.show', $conversation);
    }

    private function markRead(Conversation $conversation, ?int $userId): void
    {
        if ($userId === null) {
            return;
        }

        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}

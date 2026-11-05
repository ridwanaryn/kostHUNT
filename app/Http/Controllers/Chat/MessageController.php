<?php

declare(strict_types=1);

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\NewChatMessage;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    public function store(StoreMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('send', $conversation);

        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $message = Message::query()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $request->validated('body'),
            'read_at' => null,
        ]);

        $conversation->touch();
        $conversation->loadMissing(['tenant', 'owner']);

        $recipient = $conversation->tenant_id === $user->id
            ? $conversation->owner
            : $conversation->tenant;

        $recipient->notify(new NewChatMessage($conversation, $message));

        return redirect()->route('chat.show', $conversation);
    }
}

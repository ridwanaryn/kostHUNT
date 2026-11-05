<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Notifications\Notification;

class NewChatMessage extends Notification
{
    public function __construct(
        private readonly Conversation $conversation,
        private readonly Message $message,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'message' => 'New message from '.$this->message->sender->name.': '.$this->message->body,
            'url' => route('chat.show', $this->conversation),
        ];
    }
}

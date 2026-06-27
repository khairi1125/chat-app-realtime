<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Conversation $conversation,
        public User $recipient
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->recipient->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
        ];
    }

    public function broadcastAs(): string
    {
        return 'conversation.created';
    }
}
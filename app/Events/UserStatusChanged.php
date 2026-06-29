<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public string $status
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('user-status')];
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'status'  => $this->status,
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.status.changed';
    }
}
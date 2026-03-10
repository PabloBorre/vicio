<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message $message
    ) {}

    /**
     * Canal privado por match: private-chat.{match_id}
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->match_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-message';
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->message->id,
            'match_id'   => $this->message->match_id,
            'sender_id'  => $this->message->sender_id,
            'body'       => $this->message->body,
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}
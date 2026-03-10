<?php

namespace App\Events;

<<<<<<< HEAD
use App\Models\Message;
=======
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
<<<<<<< HEAD
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
=======
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)
     */
    public function broadcastOn(): array
    {
        return [
<<<<<<< HEAD
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
=======
            new PrivateChannel('channel-name'),
        ];
    }
}
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)

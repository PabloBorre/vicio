<?php

namespace App\Events;

<<<<<<< HEAD
<<<<<<< HEAD
use App\Models\Message;
=======
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)
=======
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
<<<<<<< HEAD
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
     * Canal privado por match: private-chat.{party_match_id}
=======
=======
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)
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
<<<<<<< HEAD
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)
=======
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)
     */
    public function broadcastOn(): array
    {
        return [
<<<<<<< HEAD
<<<<<<< HEAD
            new PrivateChannel('chat.' . $this->message->party_match_id),
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
            'party_match_id'   => $this->message->party_match_id,
            'sender_id'  => $this->message->sender_id,
            'body'       => $this->message->body,
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}
=======
=======
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)
            new PrivateChannel('channel-name'),
        ];
    }
}
<<<<<<< HEAD
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)
=======
>>>>>>> c68b8aa (Segundo commit a empezar paso 5)

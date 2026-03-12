<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        $match = $this->message->match;

        // Destinatario = el otro usuario del match
        $recipientId = $match->user1_id === $this->message->sender_id
            ? $match->user2_id
            : $match->user1_id;

        return [
            // Canal de la conversación (ambos usuarios escuchan aquí)
            new PrivateChannel('chat.' . $this->message->party_match_id),

            // Canal personal del destinatario (para notificaciones en ChatList)
            new PrivateChannel('user.' . $recipientId . '.notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-message';
    }

    public function broadcastWith(): array
    {
        return [
            'id'             => $this->message->id,
            'party_match_id' => $this->message->party_match_id,
            'sender_id'      => $this->message->sender_id,
            'body'           => $this->message->body,
            'created_at'     => $this->message->created_at->toISOString(),
        ];
    }
}
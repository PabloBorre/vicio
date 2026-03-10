<?php

namespace App\Livewire\Chat;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\PartyMatch;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatConversation extends Component
{
    public PartyMatch $match;
    public string $body = '';
    public array $messages = [];

    public function mount(PartyMatch $match): void
    {
        // Verificar que el usuario pertenece a este match
        abort_unless(
            $match->user1_id === auth()->id() || $match->user2_id === auth()->id(),
            403
        );

        $this->match = $match;
        $this->loadMessages();
        $this->markAsRead();
    }

    private function loadMessages(): void
    {
        $this->messages = Message::where('match_id', $this->match->id)
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'         => $m->id,
                'sender_id'  => $m->sender_id,
                'body'       => $m->body,
                'created_at' => $m->created_at->format('H:i'),
                'mine'       => $m->sender_id === auth()->id(),
            ])
            ->toArray();
    }

    private function markAsRead(): void
    {
        Message::where('match_id', $this->match->id)
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage(): void
    {
        $this->validate(['body' => 'required|string|max:1000']);

        $message = Message::create([
            'match_id'  => $this->match->id,
            'sender_id' => auth()->id(),
            'body'      => $this->body,
        ]);

        // Añadir a la lista local inmediatamente
        $this->messages[] = [
            'id'         => $message->id,
            'sender_id'  => $message->sender_id,
            'body'       => $message->body,
            'created_at' => $message->created_at->format('H:i'),
            'mine'       => true,
        ];

        // Broadcast al otro usuario
        broadcast(new NewMessage($message));

        $this->body = '';
        $this->dispatch('scroll-to-bottom');
    }

    /**
     * Recibe mensajes en tiempo real vía echo desde JS
     */
    #[On('echo-private:chat.{match.id},.new-message')]
    public function onNewMessage(array $data): void
    {
        // Ignorar si el mensaje es mío (ya lo añadí en sendMessage)
        if ($data['sender_id'] === auth()->id()) return;

        $this->messages[] = [
            'id'         => $data['id'],
            'sender_id'  => $data['sender_id'],
            'body'       => $data['body'],
            'created_at' => \Carbon\Carbon::parse($data['created_at'])->format('H:i'),
            'mine'       => false,
        ];

        // Marcar como leído
        Message::where('id', $data['id'])->update(['read_at' => now()]);

        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        $other = $this->match->otherUser(auth()->id());

        return view('livewire.chat.chat-conversation', [
            'other' => $other,
        ]);
    }
}
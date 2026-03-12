<?php

namespace App\Livewire\Chat;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\PartyMatch;
use App\Notifications\NewMessagePush;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatConversation extends Component
{
    public PartyMatch $match;
    public string $body = '';
    public array $messages = [];

    public function mount(PartyMatch $match): void
    {
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
        $this->messages = Message::where('party_match_id', $this->match->id)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
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
        $updated = Message::where('party_match_id', $this->match->id)
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($updated > 0) {
            $this->dispatch('unread-count-changed');
        }
    }

    public function sendMessage(): void
    {
        $this->validate(['body' => 'required|string|max:1000']);

        $message = Message::create([
            'party_match_id' => $this->match->id,
            'sender_id'      => auth()->id(),
            'body'           => $this->body,
        ]);

        $this->messages[] = [
            'id'         => $message->id,
            'sender_id'  => $message->sender_id,
            'body'       => $message->body,
            'created_at' => ($message->created_at ?? now())->format('H:i'),
            'mine'       => true,
        ];

        broadcast(new NewMessage($message));

        // Push notification al destinatario
        $recipient = $this->match->otherUser(auth()->id());
        if ($recipient->pushSubscriptions()->exists()) {
            $recipient->notify(new NewMessagePush($message, auth()->user()));
        }

        $this->body = '';
        $this->dispatch('scroll-to-bottom');
    }

    #[On('echo-private:chat.{match.id},.new-message')]
    public function onNewMessage(array $data): void
    {
        if ((int) $data['sender_id'] === auth()->id()) return;
        
        $this->messages[] = [
            'id'         => $data['id'],
            'sender_id'  => $data['sender_id'],
            'body'       => $data['body'],
            'created_at' => Carbon::parse($data['created_at'])->format('H:i'),
            'mine'       => false,
        ];

        Message::where('id', $data['id'])->update(['read_at' => now()]);
        $this->dispatch('unread-count-changed');
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.chat.chat-conversation', [
            'other' => $this->match->otherUser(auth()->id()),
        ]);
    }
}
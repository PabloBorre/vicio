<?php

namespace App\Livewire\Chat;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatNotification extends Component
{
    public int $userId;

    /** ID del match abierto actualmente (0 = ninguno) */
    public int $currentMatchId = 0;

    public function mount(int $currentMatchId = 0): void
    {
        $this->userId = auth()->id();
        $this->currentMatchId = $currentMatchId;
    }

    #[On('echo-private:user.{userId}.notifications,.new-message')]
    public function onNewMessage(array $data): void
    {
        // No mostrar si estamos en la conversación de ese match
        if ($this->currentMatchId && (int) ($data['party_match_id'] ?? 0) === $this->currentMatchId) {
            return;
        }

        $sender = User::find($data['sender_id']);
        if (! $sender) return;

        $this->dispatch('show-chat-toast',
            username: $sender->username ?? $sender->name,
            avatar: $sender->profile_photo_url,
            body: Str::limit($data['body'] ?? '', 60),
            matchId: $data['party_match_id'] ?? 0,
        );
    }

    public function render()
    {
        return view('livewire.chat.chat-notification');
    }
}

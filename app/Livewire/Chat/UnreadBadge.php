<?php

namespace App\Livewire\Chat;

use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Component;

class UnreadBadge extends Component
{
    public int $userId;
    public int $count = 0;

    public function mount(): void
    {
        $this->userId = auth()->id();
        $this->refreshCount();
    }

    #[On('echo-private:user.{userId}.notifications,.new-message')]
    public function onNewMessage(): void
    {
        $this->refreshCount();
    }

    #[On('unread-count-changed')]
    public function refreshCount(): void
    {
        $this->count = Message::whereHas('match', function ($q) {
                $q->where('user1_id', $this->userId)
                  ->orWhere('user2_id', $this->userId);
            })
            ->where('sender_id', '!=', $this->userId)
            ->whereNull('read_at')
            ->count();
    }

    public function render()
    {
        return view('livewire.chat.unread-badge');
    }
}

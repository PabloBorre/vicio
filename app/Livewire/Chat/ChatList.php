<?php

namespace App\Livewire\Chat;

use App\Models\PartyMatch;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatList extends Component
{
    public int $userId;

    public function mount(): void
    {
        $this->userId = auth()->id();
    }

    public function getMatchesProperty()
    {
        return PartyMatch::with(['user1', 'user2', 'lastMessage', 'party'])
            ->where(function ($q) {
                $q->where('user1_id', $this->userId)
                  ->orWhere('user2_id', $this->userId);
            })
            ->get()
            ->sortByDesc(fn($m) => optional($m->lastMessage)->created_at)
            ->map(function ($match) {
                $other = $match->otherUser($this->userId);
                return [
                    'match_id'          => $match->id,
                    'party_name'        => $match->party->name ?? '',
                    'username'          => $other->username ?? $other->name,
                    'profile_photo_url' => $other->profile_photo_url,
                    'last_message'      => optional($match->lastMessage)->body,
                    'last_message_time' => optional($match->lastMessage)->created_at?->diffForHumans(),
                    'unread'            => $match->unreadCount($this->userId),
                ];
            })
            ->values();
    }

    #[On('echo-private:user.{userId}.notifications,.new-message')]
    public function onNewMessageNotification(): void
    {
        // Re-render actualiza badges y último mensaje
    }

    public function render()
    {
        return view('livewire.chat.chat-list', [
            'matches' => $this->matches,
        ]);
    }
}
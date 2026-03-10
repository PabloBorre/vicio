<?php

namespace App\Livewire\Chat;

use App\Models\PartyMatch;
use Livewire\Component;

class ChatList extends Component
{
    public function getMatchesProperty()
    {
        $userId = auth()->id();

        return PartyMatch::with([
                'user1',
                'user2',
                'lastMessage',
                'party',
            ])
            ->where(function ($q) use ($userId) {
                $q->where('user1_id', $userId)
                  ->orWhere('user2_id', $userId);
            })
            ->get()
            ->sortByDesc(fn($m) => optional($m->lastMessage)->created_at)
            ->map(function ($match) use ($userId) {
                $other = $match->otherUser($userId);
                return [
                    'match_id'          => $match->id,
                    'party_name'        => $match->party->name ?? '',
                    'username'          => $other->username ?? $other->name,
                    'profile_photo_url' => $other->profile_photo_url,
                    'last_message'      => optional($match->lastMessage)->body,
                    'last_message_time' => optional($match->lastMessage)->created_at?->diffForHumans(),
                    'unread'            => $match->unreadCount($userId),
                ];
            })
            ->values();
    }

    public function render()
    {
        return view('livewire.chat.chat-list', [
            'matches' => $this->matches,
        ]);
    }
}
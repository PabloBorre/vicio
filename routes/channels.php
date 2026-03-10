<?php

use App\Models\PartyMatch;
use Illuminate\Support\Facades\Broadcast;

/**
 * Canal privado del chat: solo los dos usuarios del match pueden escuchar
 */
Broadcast::channel('chat.{matchId}', function ($user, int $matchId) {
    $match = PartyMatch::find($matchId);

    if (!$match) return false;

    return $match->user1_id === $user->id || $match->user2_id === $user->id;
});

<?php

use App\Models\PartyMatch;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{matchId}', function ($user, $matchId) {
    $match = PartyMatch::find($matchId);
    if (! $match) return false;

    return $user->id === $match->user1_id || $user->id === $match->user2_id;
});

Broadcast::channel('user.{userId}.notifications', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
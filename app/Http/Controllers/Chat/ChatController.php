<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\PartyMatch;

class ChatController extends Controller
{
    public function show(PartyMatch $match)
    {
        // Solo los participantes del match pueden entrar
        abort_unless(
            $match->user1_id === auth()->id() || $match->user2_id === auth()->id(),
            403
        );

        return view('pages.chat.show', compact('match'));
    }
}
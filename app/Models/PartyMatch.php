<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyMatch extends Model
{
    public $timestamps = false;

    protected $table = 'party_matches';

    protected $fillable = ['user1_id', 'user2_id', 'party_id'];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'match_id');
    }

    public function otherUser(int $currentUserId): User
    {
        return $this->user1_id === $currentUserId ? $this->user2 : $this->user1;
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'match_id')->latestOfMany('created_at');
    }

    public function unreadCount(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
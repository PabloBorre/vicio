<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PartyMatch;

class Message extends Model
{
    public $timestamps = false;

    protected $fillable = ['match_id', 'sender_id', 'body'];

    protected function casts(): array
    {
        return [
            'read_at'    => 'datetime',
            'created_at' => 'datetime',
        ];
    }
    
public function match()
{
    return $this->belongsTo(PartyMatch::class, 'match_id');
}

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
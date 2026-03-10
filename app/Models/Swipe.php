<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Swipe extends Model
{
    public $timestamps = false;

    protected $fillable = ['swiper_id', 'swiped_id', 'party_id', 'direction'];

    public function swiper()
    {
        return $this->belongsTo(User::class, 'swiper_id');
    }

    public function swiped()
    {
        return $this->belongsTo(User::class, 'swiped_id');
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
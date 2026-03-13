<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use App\Models\PartyMatch;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPushSubscriptions, TwoFactorAuthenticatable; 
    protected $fillable = [
        'name', 'username', 'email', 'password',
        'profile_photo_path', 'age', 'gender_identity',
        'sexual_preference', 'bio', 'is_admin', 'is_banned',
        'current_party_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'is_banned'         => 'boolean',
        ];
    }

    // Foto de perfil con fallback
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->username ?? $this->name) . '&background=49197C&color=fff';
    }

    // Iniciales para Flux avatar
    public function initials(): string
    {
        $name = $this->username ?? $this->name;
        return collect(explode(' ', $name))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');
    }

    // Relaciones
    public function parties()
    {
        return $this->belongsToMany(Party::class, 'party_user')->withPivot('joined_at');
    }

    public function currentParty()
    {
        return $this->belongsTo(Party::class, 'current_party_id');
    }

    public function swipesMade()
    {
        return $this->hasMany(Swipe::class, 'swiper_id');
    }

    public function swipesReceived()
    {
        return $this->hasMany(Swipe::class, 'swiped_id');
    }

public function matchesAsUser1()
{
    return $this->hasMany(PartyMatch::class, 'user1_id');
}

public function matchesAsUser2()
{
    return $this->hasMany(PartyMatch::class, 'user2_id');
}

// Método helper para obtener todos los matches (no es una relación Eloquent)
public function getAllMatches()
{
    return PartyMatch::where('user1_id', $this->id)
        ->orWhere('user2_id', $this->id)
        ->get();
}


}
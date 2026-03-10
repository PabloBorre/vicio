<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\PartyMatch;

class Party extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'location', 'qr_code',
        'starts_at', 'registration_opens_at', 'registration_closes_at',
        'status', 'cover_image', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'               => 'datetime',
            'registration_opens_at'   => 'datetime',
            'registration_closes_at'  => 'datetime',
        ];
    }

    // Genera QR automáticamente al crear
    protected static function booted(): void
    {
        static::creating(function (Party $party) {
            $party->qr_code = (string) Str::uuid();
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'party_user')->withPivot('joined_at');
    }

    public function swipes()
    {
        return $this->hasMany(Swipe::class);
    }

public function matches()
{
    return $this->hasMany(PartyMatch::class);
}

    // URL del QR
    public function getQrUrlAttribute(): string
    {
        return route('party.register', ['qr' => $this->qr_code]);
    }

    // ¿Está activa?
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Segundos restantes para el inicio
    public function secondsUntilStart(): int
    {
        return max(0, now()->diffInSeconds($this->starts_at, false));
    }
}
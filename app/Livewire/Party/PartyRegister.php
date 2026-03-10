<?php

namespace App\Livewire\Party;

use App\Models\Party;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class PartyRegister extends Component
{
    use WithFileUploads;

    public Party $party;
    public int $step = 1;
    public int $totalSteps = 3;

    // Paso 1
    #[Validate('required|string|min:3|max:30|unique:users,username')]
    public string $username = '';

    #[Validate('nullable|image|max:5120')] // 5MB max
    public $photo = null;

    // Paso 2
    #[Validate('required|integer|min:18|max:99')]
    public string $age = '';

    #[Validate('required|string')]
    public string $gender_identity = '';

    #[Validate('required|string')]
    public string $sexual_preference = '';

    // Paso 3
    #[Validate('required|string|min:10|max:500')]
    public string $bio = '';

    public function mount(Party $party): void
    {
        $this->party = $party;

        // Si ya tiene cuenta y perfil completo, saltamos al paso de unirse
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->username) {
                $this->username = $user->username;
            }
        }
    }

    public function nextStep(): void
    {
        $this->validateStep();
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    private function validateStep(): void
    {
        match($this->step) {
            1 => $this->validateOnly('username'),
            2 => $this->validateOnly(['age', 'gender_identity', 'sexual_preference']),
            3 => $this->validateOnly('bio'),
        };
    }

    public function register(): void
    {
        // Validar todo antes de guardar
        $this->validate([
            'username'          => 'required|string|min:3|max:30|unique:users,username' . (Auth::check() ? ',' . Auth::id() : ''),
            'age'               => 'required|integer|min:18|max:99',
            'gender_identity'   => 'required|string',
            'sexual_preference' => 'required|string',
            'bio'               => 'required|string|min:10|max:500',
        ]);

        // Subir foto si existe
        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('profile-photos', 'public');
        }

        if (Auth::check()) {
            // Usuario existente → actualizar perfil
            $user = Auth::user();
            $user->update([
                'username'          => $this->username,
                'age'               => $this->age,
                'gender_identity'   => $this->gender_identity,
                'sexual_preference' => $this->sexual_preference,
                'bio'               => $this->bio,
                'profile_photo_path' => $photoPath ?? $user->profile_photo_path,
                'current_party_id'  => $this->party->id,
            ]);
        } else {
            // Usuario nuevo → crear cuenta sin email/password
            $user = User::create([
                'name'              => $this->username,
                'username'          => $this->username,
                'age'               => $this->age,
                'gender_identity'   => $this->gender_identity,
                'sexual_preference' => $this->sexual_preference,
                'bio'               => $this->bio,
                'profile_photo_path' => $photoPath,
                'current_party_id'  => $this->party->id,
                'password'          => null,
            ]);

            Auth::login($user);
        }

        // Unir usuario a la fiesta
        $user->parties()->syncWithoutDetaching([
            $this->party->id => ['joined_at' => now()]
        ]);

        // Redirigir a sala de espera
        $this->redirect(route('party.waiting', $this->party->qr_code), navigate: true);
    }

    public function render()
    {
        return view('livewire.party.party-register');
    }
}
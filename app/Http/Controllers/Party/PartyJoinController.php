<?php

namespace App\Http\Controllers\Party;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Support\ImageHelper;
use Illuminate\Validation\Rules\Password;

class PartyJoinController extends Controller
{
    private function redirectIfFinished(): \Illuminate\Http\RedirectResponse
    {
        return auth()->check()
            ? redirect()->route('dashboard')
            : redirect()->route('home');
    }

    public function show(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'finished') {
            return $this->redirectIfFinished();
        }

        if (auth()->check()) {
            $user = auth()->user();
            $isMember = $user->parties()->where('party_id', $party->id)->exists();

            if ($isMember) {
                return $this->redirectToPartyStage($party, $qr);
            }
        }

        return view('pages.party.show', compact('party'));
    }

    public function register(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'finished') {
            return $this->redirectIfFinished();
        }

        if (! auth()->check()) {
            session([
                'intended_party_qr' => $qr,
                'url.intended'      => route('party.register', $qr),
            ]);
            return redirect()->route('login');
        }

        $user = auth()->user();
        $isMember = $user->parties()->where('party_id', $party->id)->exists();

        if ($isMember) {
            return $this->redirectToPartyStage($party, $qr);
        }

        $user->update(['current_party_id' => $party->id]);
        $user->parties()->syncWithoutDetaching([
            $party->id => ['joined_at' => now()]
        ]);

        return $this->redirectToPartyStage($party, $qr);
    }

    public function store(Request $request, string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'finished') {
            return $this->redirectIfFinished();
        }

        $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'username'           => ['required', 'string', 'min:3', 'max:30', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email'              => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'           => ['required', 'confirmed', Password::min(8)],
            'age'                => ['required', 'integer', 'min:18', 'max:99'],
            'gender_identity'    => ['required', 'string', 'in:man,woman'],
            'sexual_preference'  => ['required', 'string', 'in:man,woman,both'],
            'bio'                => ['required', 'string', 'min:10', 'max:500'],
            'profile_photo'      => ['required', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'name.required'              => 'El nombre es obligatorio.',
            'username.required'          => 'El nombre de usuario es obligatorio.',
            'username.unique'            => 'Este nombre de usuario ya está en uso.',
            'username.regex'             => 'El nombre de usuario solo puede contener letras, números y guiones bajos.',
            'email.required'             => 'El email es obligatorio.',
            'email.unique'               => 'Este email ya está registrado.',
            'password.required'          => 'La contraseña es obligatoria.',
            'password.confirmed'         => 'Las contraseñas no coinciden.',
            'age.required'               => 'La edad es obligatoria.',
            'age.min'                    => 'Debes tener al menos 18 años.',
            'gender_identity.required'   => 'Selecciona con qué te identificas.',
            'sexual_preference.required' => 'Selecciona tu preferencia.',
            'bio.required'               => 'Cuéntanos algo sobre ti.',
            'bio.min'                    => 'La bio debe tener al menos 10 caracteres.',
            'profile_photo.required'     => 'La foto de perfil es obligatoria.',
            'profile_photo.image'        => 'El archivo debe ser una imagen.',
            'sexual_preference.in' => 'Selecciona una preferencia válida.',
            'gender_identity.in'   => 'Selecciona una identidad válida.',
        ]);

        $photoPath = ImageHelper::storeAsWebP($request->file('profile_photo'));
        $user = User::create([
            'name'               => $request->name,
            'username'           => $request->username,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'age'                => $request->age,
            'gender_identity'    => $request->gender_identity,
            'sexual_preference'  => $request->sexual_preference,
            'bio'                => $request->bio,
            'profile_photo_path' => $photoPath,
            'current_party_id'   => $party->id,
        ]);

        $user->parties()->syncWithoutDetaching([
            $party->id => ['joined_at' => now()]
        ]);

        Auth::login($user);

        return $this->redirectToPartyStage($party, $qr);
    }

    public function waiting(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'finished') {
            return $this->redirectIfFinished();
        }

        if ($party->status === 'active') {
            return redirect()->route('party.swipe', $qr);
        }

        if (!auth()->user()->parties()->where('party_id', $party->id)->exists()) {
            return redirect()->route('party.show', $qr);
        }

        return view('pages.party.waiting', compact('party'));
    }

    public function swipe(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'finished') {
            return $this->redirectIfFinished();
        }

        if ($party->status !== 'active') {
            return redirect()->route('party.waiting', $qr);
        }

        if (!auth()->user()->parties()->where('party_id', $party->id)->exists()) {
            return redirect()->route('party.show', $qr);
        }

        return view('pages.party.swipe', compact('party'));
    }

    private function redirectToPartyStage(Party $party, string $qr)
    {
        return match(true) {
            $party->status === 'active'   => redirect()->route('party.swipe', $qr),
            $party->status === 'finished' => $this->redirectIfFinished(),
            default                       => redirect()->route('party.waiting', $qr),
        };
    }
}
<?php

namespace App\Http\Controllers\Party;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PartyJoinController extends Controller
{
    /**
     * Muestra la página de bienvenida de la fiesta al escanear el QR.
     */
    public function show(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'finished') {
            abort(410, 'Esta fiesta ha finalizado.');
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

    /**
     * Muestra el formulario de registro en la fiesta.
     * - Si no está autenticado → formulario de creación de cuenta
     * - Si está autenticado pero no es miembro → unirlo directamente
     */
    public function register(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'finished') {
            abort(410, 'Esta fiesta ha finalizado.');
        }

        if (auth()->check()) {
            $isMember = auth()->user()->parties()->where('party_id', $party->id)->exists();
            if ($isMember) {
                return $this->redirectToPartyStage($party, $qr);
            }

            // Ya tiene cuenta pero aún no está en la fiesta → unirlo directamente
            $user = auth()->user();
            $user->update(['current_party_id' => $party->id]);
            $user->parties()->syncWithoutDetaching([
                $party->id => ['joined_at' => now()]
            ]);

            return $this->redirectToPartyStage($party, $qr);
        }

        // No autenticado → mostrar formulario de creación de cuenta
        return view('pages.party.register', compact('party'));
    }

    /**
     * Procesa el registro de cuenta nueva + unirse a la fiesta.
     */
    public function store(Request $request, string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'finished') {
            abort(410, 'Esta fiesta ha finalizado.');
        }

        $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'username'           => ['required', 'string', 'min:3', 'max:30', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email'              => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'           => ['required', 'confirmed', Password::min(8)],
            'age'                => ['required', 'integer', 'min:18', 'max:99'],
            'gender_identity'    => ['required', 'string', 'in:man,woman,non_binary,other'],
            'sexual_preference'  => ['required', 'string', 'in:hetero,homo,bi,pan'],
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
        ]);

        $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');

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

        // Unir al usuario a la fiesta
        $user->parties()->syncWithoutDetaching([
            $party->id => ['joined_at' => now()]
        ]);

        Auth::login($user);

        return $this->redirectToPartyStage($party, $qr);
    }

    /**
     * Sala de espera con countdown.
     */
    public function waiting(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'active') {
            return redirect()->route('party.swipe', $qr);
        }

        if ($party->status === 'finished') {
            abort(410, 'Esta fiesta ha finalizado.');
        }

        if (!auth()->user()->parties()->where('party_id', $party->id)->exists()) {
            return redirect()->route('party.show', $qr);
        }

        return view('pages.party.waiting', compact('party'));
    }

    /**
     * Vista de swipe.
     */
    public function swipe(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status !== 'active') {
            return redirect()->route('party.waiting', $qr);
        }

        if (!auth()->user()->parties()->where('party_id', $party->id)->exists()) {
            return redirect()->route('party.show', $qr);
        }

        return view('pages.party.swipe', compact('party'));
    }

    /**
     * Redirige al usuario al estado correcto de la fiesta.
     */
    private function redirectToPartyStage(Party $party, string $qr)
    {
        return match(true) {
            $party->status === 'active'   => redirect()->route('party.swipe', $qr),
            $party->status === 'finished' => abort(410, 'Esta fiesta ha finalizado.'),
            default                       => redirect()->route('party.waiting', $qr),
        };
    }
}
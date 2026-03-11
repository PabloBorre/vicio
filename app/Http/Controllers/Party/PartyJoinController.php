<?php

namespace App\Http\Controllers\Party;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;

class PartyJoinController extends Controller
{
    /**
     * Muestra la página de bienvenida de la fiesta al escanear el QR.
     */
    public function show(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        // Si la fiesta está finalizada, mostramos error
        if ($party->status === 'finished') {
            abort(410, 'Esta fiesta ha finalizado.');
        }

        // Si el usuario ya está autenticado y es miembro de esta fiesta
        if (auth()->check()) {
            $user = auth()->user();
            $isMember = $user->parties()->where('party_id', $party->id)->exists();

            if ($isMember) {
                // Ya está registrado → mandarlo a sala de espera o swipe
                return $this->redirectToPartyStage($party, $qr);
            }
        }

        return view('pages.party.show', compact('party'));
    }

    /**
     * Muestra el formulario de registro en la fiesta.
     */
    public function register(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        if ($party->status === 'finished') {
            abort(410, 'Esta fiesta ha finalizado.');
        }

        // Si ya está registrado en esta fiesta, redirigir
        if (auth()->check()) {
            $isMember = auth()->user()->parties()->where('party_id', $party->id)->exists();
            if ($isMember) {
                return $this->redirectToPartyStage($party, $qr);
            }
        }

        return view('pages.party.register', compact('party'));
    }

    /**
     * Sala de espera con countdown.
     */
    public function waiting(string $qr)
    {
        $party = Party::where('qr_code', $qr)->firstOrFail();

        // Si ya está activa, mandar al swipe directamente
        if (in_array($party->status, ['active', 'countdown'])) {
    return redirect()->route('party.swipe', $qr);
}

        // Verificar que el usuario es miembro
        if (!auth()->user()->parties()->where('party_id', $party->id)->exists()) {
            return redirect()->route('party.show', $qr);
        }

        return view('pages.party.waiting', compact('party'));
    }

    /**
     * Vista de swipe (se completará en paso 5).
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
        return match($party->status) {
            'active'   => redirect()->route('party.swipe', $qr),
            'finished' => abort(410, 'Esta fiesta ha finalizado.'),
            default    => redirect()->route('party.waiting', $qr),
        };
    }
}
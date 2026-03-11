<?php

namespace App\Livewire\Party;

use App\Models\Party;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PartyRegister extends Component
{
    public Party $party;

    public function mount(Party $party): void
    {
        $this->party = $party;

        // Usuario siempre autenticado con perfil completo → unir y redirigir
        $user = Auth::user();
        $user->update(['current_party_id' => $party->id]);
        $user->parties()->syncWithoutDetaching([
            $party->id => ['joined_at' => now()]
        ]);

        $this->redirect(route('party.waiting', $party->qr_code), navigate: true);
    }

    public function render()
    {
        return view('livewire.party.party-register');
    }
}
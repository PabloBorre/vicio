<?php

namespace App\Livewire\Party;

use App\Models\Party;
use Livewire\Attributes\On;
use Livewire\Component;

class PartyWaiting extends Component
{
    public Party $party;
    public int $secondsLeft = 0;
    public int $attendees = 0;

    public function mount(Party $party): void
    {
        $this->party = $party;
        $this->secondsLeft = $party->secondsUntilStart();
        $this->attendees = $party->users()->count();

        // Si ya está activa, redirigir al swipe
        if ($party->status === 'active') {
            $this->redirect(route('party.swipe', $party->qr_code), navigate: true);
        }
    }

    /**
     * Se llama cada segundo desde Alpine.js vía wire:poll o desde un evento.
     * Comprueba si la fiesta ha arrancado.
     */
    public function checkPartyStatus(): void
    {
        $this->party->refresh();
        $this->secondsLeft = $this->party->secondsUntilStart();
        $this->attendees = $this->party->users()->count();

        if ($this->party->status === 'active') {
            $this->redirect(route('party.swipe', $this->party->qr_code), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.party.party-waiting');
    }
}
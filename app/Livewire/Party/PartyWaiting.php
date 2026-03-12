<?php

namespace App\Livewire\Party;

use App\Models\Party;
use Livewire\Component;

class PartyWaiting extends Component
{
    public Party $party;
    public int $secondsLeft = 0;
    public int $attendees   = 0;

    public function mount(Party $party): void
    {
        $this->party      = $party;
        $this->secondsLeft = max(0, (int) now()->diffInSeconds($party->starts_at, false));
        $this->attendees  = $party->users()->count();
    }

    public function checkPartyStatus(): void
    {
        $this->party->refresh();
        $this->attendees   = $this->party->users()->count();
        $this->secondsLeft = max(0, (int) now()->diffInSeconds($this->party->starts_at, false));

        // Si starts_at ya pasó y la fiesta sigue en registration, activarla aquí mismo
        if (
            $this->party->status === 'registration'
            && now()->gte($this->party->starts_at)
        ) {
            $this->party->update(['status' => 'active']);
            $this->party->refresh();
        }

        if ($this->party->status === 'active') {
            $this->redirect(route('party.swipe', $this->party->qr_code), navigate: true);
            return;
        }

        if ($this->party->status === 'finished') {
            $this->redirect(route('party.show', $this->party->qr_code), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.party.party-waiting');
    }
}
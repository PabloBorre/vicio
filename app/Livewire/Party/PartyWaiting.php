<?php

namespace App\Livewire\Party;

use App\Models\Party;
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
        $this->attendees   = $party->users()->count();
    }

    public function checkPartyStatus(): void
    {
        $this->party->refresh();
        $this->secondsLeft = $this->party->secondsUntilStart();
        $this->attendees   = $this->party->users()->count();

        if ($this->party->status === 'active') {
            $this->redirectRoute('party.swipe', $this->party->qr_code);
        }
    }

    public function render()
    {
        return view('livewire.party.party-waiting', [
            'party'      => $this->party,
            'secondsLeft' => $this->secondsLeft,
            'attendees'  => $this->attendees,
        ]);
    }
}
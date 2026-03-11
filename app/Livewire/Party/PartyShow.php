<?php

namespace App\Livewire\Party;

use App\Models\Party;
use Livewire\Component;

class PartyShow extends Component
{
    public Party $party;
    public int $secondsLeft = 0;
    public int $attendees = 0;

    public function mount(Party $party): void
    {
        $this->party      = $party;
        $this->secondsLeft = max(0, (int) now()->diffInSeconds($party->starts_at, false));
        $this->attendees  = $party->users()->count();
    }

    public function checkStatus(): void
    {
        $this->party->refresh();
        $this->secondsLeft = max(0, (int) now()->diffInSeconds($this->party->starts_at, false));
        $this->attendees   = $this->party->users()->count();
    }

    public function render()
    {
        return view('livewire.party.party-show');
    }
}   
<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\On;
use Livewire\Component;

class BanWatcher extends Component
{
    public int $userId;

    public function mount(): void
    {
        $this->userId = auth()->id();
    }

    #[On('echo-private:user.{userId},.banned')]
    public function onBanned(): void
    {
        $this->dispatch('force-logout');
    }

    public function render()
    {
        return view('livewire.auth.ban-watcher');
    }
}

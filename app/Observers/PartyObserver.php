<?php

namespace App\Observers;

use App\Models\Party;

class PartyObserver
{
    /**
     * Handle the Party "created" event.
     */
    public function created(Party $party): void
    {
        //
    }

    public function updated(Party $party): void
    {
        // Solo actuar cuando el status cambia a 'finished'
        if ($party->wasChanged('status') && $party->status === 'finished') {
            // Los mensajes se borran en cascada al borrar los matches
            $party->matches()->delete();
        }
    }

    /**
     * Handle the Party "deleted" event.
     */
    public function deleted(Party $party): void
    {
        //
    }

    /**
     * Handle the Party "restored" event.
     */
    public function restored(Party $party): void
    {
        //
    }

    /**
     * Handle the Party "force deleted" event.
     */
    public function forceDeleted(Party $party): void
    {
        //
    }
}

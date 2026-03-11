<?php

namespace App\Console\Commands;

use App\Models\Party;
use Illuminate\Console\Command;

class UpdatePartyStatuses extends Command
{
    protected $signature   = 'parties:update-statuses';
    protected $description = 'Actualiza el estado de las fiestas según fecha/hora actual';

    public function handle(): void
    {
        $now = now();

        // registration → active: cuando llega starts_at
        Party::where('status', 'registration')
            ->where('starts_at', '<=', $now)
            ->update(['status' => 'active']);

        // active → finished: 10 horas después de starts_at
        Party::where('status', 'active')
            ->where('starts_at', '<=', $now->copy()->subHours(10))
            ->update(['status' => 'finished']);

        $this->info('Estados actualizados correctamente.');
    }
}
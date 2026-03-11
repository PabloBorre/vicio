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

        // draft → registration: cuando abre el registro
        Party::where('status', 'draft')
            ->whereNotNull('registration_opens_at')
            ->where('registration_opens_at', '<=', $now)
            ->update(['status' => 'registration']);

        // Sin registration_opens_at → pasa a registration si falta menos de 24h
        Party::where('status', 'draft')
            ->whereNull('registration_opens_at')
            ->where('starts_at', '<=', $now->copy()->addHours(24))
            ->update(['status' => 'registration']);

        // registration → active: cuando llega starts_at
        // (countdown también pasa a active por si hay fiestas antiguas con ese estado)
        Party::whereIn('status', ['registration', 'countdown'])
            ->where('starts_at', '<=', $now)
            ->update(['status' => 'active']);

        // active → finished: 10 horas después de starts_at
        Party::where('status', 'active')
            ->where('starts_at', '<=', $now->copy()->subHours(10))
            ->update(['status' => 'finished']);

        $this->info('Estados actualizados correctamente.');
    }
}
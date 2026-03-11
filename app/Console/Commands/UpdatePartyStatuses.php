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

        // Sin registration_opens_at → pasa directo a registration si falta menos de 24h
        Party::where('status', 'draft')
            ->whereNull('registration_opens_at')
            ->where('starts_at', '<=', $now->copy()->addHours(24))
            ->update(['status' => 'registration']);

        // registration → countdown: cuando cierra el registro (o 1h antes del inicio)
        Party::where('status', 'registration')
            ->where(function ($q) use ($now) {
                $q->where(function ($q2) use ($now) {
                    $q2->whereNotNull('registration_closes_at')
                       ->where('registration_closes_at', '<=', $now);
                })->orWhere(function ($q2) use ($now) {
                    $q2->whereNull('registration_closes_at')
                       ->where('starts_at', '<=', $now->copy()->addHour());
                });
            })
            ->update(['status' => 'countdown']);

        // countdown → active: cuando llega starts_at
        Party::where('status', 'countdown')
            ->where('starts_at', '<=', $now)
            ->update(['status' => 'active']);

        // active → finished: 10 horas después de starts_at (ajústalo a tu gusto)
        Party::where('status', 'active')
            ->where('starts_at', '<=', $now->copy()->subHours(10))
            ->update(['status' => 'finished']);

        $this->info('Estados actualizados correctamente.');
    }
}
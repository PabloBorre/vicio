<?php

namespace App\Http\Controllers;

use App\Models\Party;

class PartiesController extends Controller
{
    public function index()
    {
        // Activar fiestas cuyo starts_at ya pasó
        Party::whereIn('status', ['registration', 'countdown'])
            ->where('starts_at', '<=', now())
            ->update(['status' => 'active']);

        // Finalizar fiestas activas que llevan más de 10 horas
        Party::where('status', 'active')
            ->where('starts_at', '<=', now()->subHours(10))
            ->update(['status' => 'finished']);

        // Últimas 2 finalizadas
        $finished = Party::where('status', 'finished')
            ->orderByDesc('starts_at')
            ->limit(2)
            ->get();

        // Activa actual (si existe)
        $active = Party::where('status', 'active')
            ->orderByDesc('starts_at')
            ->first();

        // Próximas 2 (registration)
        $upcoming = Party::where('status', 'registration')
            ->orderBy('starts_at')
            ->limit(2)
            ->get();

        return view('pages.parties', compact('finished', 'active', 'upcoming'));
    }
}
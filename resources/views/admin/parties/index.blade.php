<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-dvh" style="background-color: #0a0212;">
<div class="w-full max-w-[430px] mx-auto flex flex-col" style="min-height: 100dvh;">

    {{-- Header --}}
    <div class="shrink-0 flex items-center justify-between px-4 py-3 border-b border-zinc-800">
        <a href="{{ route('dashboard') }}" wire:navigate
            class="size-9 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center hover:bg-zinc-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="text-white font-bold text-base">Fiestas</span>
        <a href="{{ route('admin.parties.create') }}" wire:navigate
            class="size-9 rounded-full vicio-gradient flex items-center justify-center hover:opacity-90 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mx-4 mt-4 bg-green-900/30 border border-green-700/50 text-green-400 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Lista --}}
    <div class="flex-1 px-4 py-4 space-y-3">

        @if($parties->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-center gap-4">
                <div class="size-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-7 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-zinc-300 font-semibold">No hay fiestas todavía</p>
                    <p class="text-zinc-600 text-sm mt-1">Pulsa + para crear la primera</p>
                </div>
            </div>
        @else
            @foreach($parties as $party)
                @php
                    $statusConfig = [
                        'draft'        => ['label' => 'Borrador',    'color' => 'bg-zinc-800 text-zinc-400'],
                        'registration' => ['label' => 'Registro',    'color' => 'bg-blue-900/50 text-blue-400'],
                        'countdown'    => ['label' => 'Cuenta atrás','color' => 'bg-yellow-900/50 text-yellow-400'],
                        'active'       => ['label' => 'Activa',      'color' => 'bg-green-900/50 text-green-400'],
                        'finished'     => ['label' => 'Finalizada',  'color' => 'bg-zinc-800 text-zinc-500'],
                    ];
                    $cfg = $statusConfig[$party->status] ?? $statusConfig['draft'];
                @endphp

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
                    {{-- Cover --}}
                    @if($party->cover_image)
                        <div class="h-28 overflow-hidden">
                            <img src="{{ asset('storage/' . $party->cover_image) }}" class="w-full h-full object-cover"/>
                        </div>
                    @else
                        <div class="h-20 vicio-gradient flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-8 fill-white/30" viewBox="0 0 24 24">
                                <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10z"/>
                            </svg>
                        </div>
                    @endif

                    <div class="px-4 py-3 space-y-3">
                        {{-- Nombre + estado --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-white font-semibold truncate">{{ $party->name }}</p>
                                @if($party->location)
                                    <p class="text-zinc-500 text-xs mt-0.5 truncate">📍 {{ $party->location }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-medium {{ $cfg['color'] }}">
                                {{ $cfg['label'] }}
                            </span>
                        </div>

                        {{-- Meta --}}
                        <div class="flex items-center gap-4 text-xs text-zinc-500">
                            <span>📅 {{ $party->starts_at->format('d/m/Y H:i') }}</span>
                            <span>👥 {{ $party->users_count }} asistentes</span>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex items-center gap-2 pt-1 border-t border-zinc-800">
                            {{-- QR --}}
                            <a href="{{ route('admin.parties.qr', $party) }}" target="_blank"
                                title="Ver QR"
                                class="flex-1 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center gap-1.5 text-zinc-400 text-xs font-medium transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                QR
                            </a>

                            {{-- Usuarios --}}
                            <a href="{{ route('admin.users.index', ['party' => $party->id]) }}" wire:navigate
                                title="Ver usuarios"
                                class="flex-1 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center gap-1.5 text-zinc-400 text-xs font-medium transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Usuarios
                            </a>

                            {{-- Editar --}}
                            <a href="{{ route('admin.parties.edit', $party) }}" wire:navigate
                                title="Editar"
                                class="flex-1 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center gap-1.5 text-zinc-400 text-xs font-medium transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>

                            {{-- Eliminar --}}
                            <form method="POST" action="{{ route('admin.parties.destroy', $party) }}"
                                onsubmit="return confirm('¿Eliminar esta fiesta?')" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full py-2 rounded-xl bg-zinc-800 hover:bg-red-900/50 flex items-center justify-center gap-1.5 text-zinc-400 hover:text-red-400 text-xs font-medium transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Borrar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

</div>
@fluxScripts
</body>
</html>
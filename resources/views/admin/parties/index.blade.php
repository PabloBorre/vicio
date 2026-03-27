<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="overflow-hidden" style="background-color: #A678C8;">
<div class="w-full max-w-[430px] mx-auto flex flex-col" style="height: 100dvh; background-color: #A678C8;">

    {{-- Header --}}
    {{-- Header --}}
<div class="shrink-0 flex items-center px-4 py-4 gap-3" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
    <a href="{{ route('dashboard') }}" wire:navigate
        class="shrink-0 size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
        style="background-color: rgba(255,255,255,0.2);">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h1 class="text-white font-bold text-xl flex-1 leading-tight">Fiestas</h1>
    <a href="{{ route('admin.parties.create') }}" wire:navigate
        class="shrink-0 size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
        style="background-color: #2d0a3e;">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
    </a>
    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
        @csrf
        <button type="submit"
            class="size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
            style="background-color: rgba(255,255,255,0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        </button>
    </form>
</div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mx-4 mt-4 rounded-2xl px-4 py-3 text-sm font-medium text-white" style="background-color: rgba(74,222,128,0.2); border: 1px solid rgba(74,222,128,0.3);">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Lista --}}
    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-3">

        @if($parties->isEmpty())
            <div class="flex flex-col items-center justify-center py-32 text-center gap-4 px-6">
                <div class="text-5xl">🎉</div>
                <p class="font-semibold text-white text-base">No hay fiestas todavía</p>
                <p class="text-sm" style="color: rgba(255,255,255,0.6);">Pulsa + para crear la primera</p>
            </div>
        @else
            @foreach($parties as $party)
                @php
                    $statusConfig = [
                        'draft'        => ['label' => 'Borrador',     'bg' => 'rgba(0,0,0,0.2)',           'color' => 'rgba(255,255,255,0.4)'],
                        'registration' => ['label' => 'Registro',     'bg' => 'rgba(96,165,250,0.2)',      'color' => '#93c5fd'],
                        'countdown'    => ['label' => 'Cuenta atrás', 'bg' => 'rgba(251,191,36,0.2)',      'color' => '#fcd34d'],
                        'active'       => ['label' => 'Activa',       'bg' => 'rgba(74,222,128,0.2)',      'color' => '#4ade80'],
                        'finished'     => ['label' => 'Finalizada',   'bg' => 'rgba(0,0,0,0.2)',           'color' => 'rgba(255,255,255,0.35)'],
                    ];
                    $cfg = $statusConfig[$party->status] ?? $statusConfig['draft'];
                    $isFinished = $party->status === 'finished';
                @endphp

                <div class="rounded-2xl overflow-hidden" style="background-color: {{ $isFinished ? 'rgba(0,0,0,0.15)' : 'rgba(255,255,255,0.15)' }};">

                    {{-- Cover --}}
                    @if($party->cover_image)
                        <div class="h-24 overflow-hidden">
                            <img src="{{ asset('storage/' . $party->cover_image) }}"
                                class="w-full h-full object-cover {{ $isFinished ? 'opacity-40' : 'opacity-80' }}"
                                alt="{{ $party->name }}">
                        </div>
                    @endif

                    <div class="px-4 py-3 space-y-3">
                        {{-- Nombre + estado --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <h2 class="font-bold text-base leading-tight truncate {{ $isFinished ? '' : 'text-white' }}"
                                    style="{{ $isFinished ? 'color: rgba(255,255,255,0.55);' : '' }}">
                                    {{ $party->name }}
                                </h2>
                                @if($party->location)
                                    <p class="text-xs mt-0.5 flex items-center gap-1"
                                        style="color: {{ $isFinished ? 'rgba(255,255,255,0.35)' : 'rgba(255,255,255,0.6)' }};">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $party->location }}
                                    </p>
                                @endif
                            </div>
                            <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold flex items-center gap-1"
                                style="background-color: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }};">
                                @if($party->status === 'active')
                                    <span class="size-1.5 rounded-full bg-green-400 animate-pulse inline-block"></span>
                                @endif
                                {{ $cfg['label'] }}
                            </span>
                        </div>

                        {{-- Meta --}}
                        <div class="flex items-center gap-3 text-xs" style="color: {{ $isFinished ? 'rgba(255,255,255,0.35)' : 'rgba(255,255,255,0.55)' }};">
                            <span> {{ $party->starts_at->format('d M · H:i') }}</span>
                            <span> {{ $party->users_count }} asistentes</span>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex items-center gap-2 pt-1" style="border-top: 1px solid rgba(255,255,255,0.1);">

                            {{-- QR --}}
                            <a href="{{ route('admin.parties.qr', $party) }}" target="_blank"
                                class="flex-1 py-2 rounded-xl flex items-center justify-center gap-1.5 text-xs font-medium transition-opacity hover:opacity-80"
                                style="background-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                QR
                            </a>

                            {{-- Usuarios --}}
                            <a href="{{ route('admin.users.index', ['party' => $party->id]) }}" wire:navigate
                                class="flex-1 py-2 rounded-xl flex items-center justify-center gap-1.5 text-xs font-medium transition-opacity hover:opacity-80"
                                style="background-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Usuarios
                            </a>

                            {{-- Estado --}}
                            <form method="POST" action="{{ route('admin.parties.status', $party) }}" class="flex-1">
                                @csrf @method('PATCH')
                                @php
                                    $nextStatus = match($party->status) {
                                        'registration' => 'active',
                                        'active'       => 'finished',
                                        default        => null,
                                    };
                                    $nextLabel = match($party->status) {
                                        'registration' => 'Activar',
                                        'active'       => 'Finalizar',
                                        default        => null,
                                    };
                                @endphp
                                @if($nextStatus)
                                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                                    <button type="submit"
                                        class="w-full py-2 rounded-xl flex items-center justify-center gap-1.5 text-xs font-medium transition-opacity hover:opacity-80"
                                        style="background-color: #2d0a3e; color: rgba(255,255,255,0.9);">
                                        {{ $nextLabel }}
                                    </button>
                                @else
                                    <div class="w-full py-2 rounded-xl flex items-center justify-center text-xs font-medium"
                                        style="background-color: rgba(0,0,0,0.15); color: rgba(255,255,255,0.3);">
                                        —
                                    </div>
                                @endif
                            </form>

                            {{-- Editar --}}
                            <a href="{{ route('admin.parties.edit', $party) }}" wire:navigate
                                class="size-9 rounded-xl flex items-center justify-center shrink-0 transition-opacity hover:opacity-80"
                                style="background-color: rgba(255,255,255,0.15);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: rgba(255,255,255,0.8);">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- Eliminar --}}
                            <form method="POST" action="{{ route('admin.parties.destroy', $party) }}"
                                onsubmit="return confirm('¿Eliminar «{{ addslashes($party->name) }}»?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="size-9 rounded-xl flex items-center justify-center transition-opacity hover:opacity-80"
                                    style="background-color: rgba(239,68,68,0.2);">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #fca5a5;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
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
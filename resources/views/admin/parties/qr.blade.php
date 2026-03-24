<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
    <title>QR — {{ $party->name }}</title>
</head>
<body style="background-color: #A678C8;">
<div class="w-full max-w-[430px] mx-auto flex flex-col" style="min-height: 100dvh; background-color: #A678C8;">

    @php
        $registerUrl = route('party.register', $party->qr_code);
        $qrImageUrl  = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&margin=20&color=2d0a3e&bgcolor=ffffff&data=' . urlencode($registerUrl);
    @endphp

    {{-- Header --}}
    <div class="shrink-0 flex items-center px-4 py-4 gap-3" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
        <a href="{{ route('admin.parties.index') }}" wire:navigate
            class="shrink-0 size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
            style="background-color: rgba(255,255,255,0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-white font-bold text-xl leading-tight truncate">QR — {{ $party->name }}</h1>
            <p class="text-xs" style="color: rgba(255,255,255,0.6);">Escanea para unirte a la fiesta</p>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-8 gap-6">

        {{-- QR Code --}}
        <div class="rounded-3xl overflow-hidden shadow-2xl p-4 bg-white" style="width: 280px; height: 280px;">
            <img
                src="{{ $qrImageUrl }}"
                alt="QR {{ $party->name }}"
                class="w-full h-full object-contain"
            >
        </div>

        {{-- Info fiesta --}}
        <div class="w-full rounded-2xl px-5 py-4 space-y-2 text-center" style="background-color: rgba(255,255,255,0.15);">
            <h2 class="text-white font-bold text-lg">{{ $party->name }}</h2>
            @if($party->location)
                <p class="text-sm flex items-center justify-center gap-1.5" style="color: rgba(255,255,255,0.7);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $party->location }}
                </p>
            @endif
            <p class="text-sm" style="color: rgba(255,255,255,0.7);">
                📅 {{ $party->starts_at->format('d M Y · H:i') }}
            </p>

            {{-- Badge estado --}}
            @php
                $statusConfig = [
                    'registration' => ['label' => 'Registro abierto', 'color' => '#93c5fd', 'bg' => 'rgba(96,165,250,0.2)'],
                    'active'       => ['label' => 'Activa ahora',     'color' => '#4ade80', 'bg' => 'rgba(74,222,128,0.2)'],
                    'finished'     => ['label' => 'Finalizada',       'color' => 'rgba(255,255,255,0.4)', 'bg' => 'rgba(0,0,0,0.2)'],
                ];
                $cfg = $statusConfig[$party->status] ?? $statusConfig['registration'];
            @endphp
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold mt-1"
                style="background-color: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }};">
                @if($party->status === 'active')
                    <span class="size-1.5 rounded-full bg-green-400 inline-block" style="animation: pulse 1.5s infinite;"></span>
                @endif
                {{ $cfg['label'] }}
            </div>
        </div>

        {{-- URL --}}
        <div class="w-full space-y-2">
            <p class="text-xs text-center" style="color: rgba(255,255,255,0.5);">URL de registro</p>
            <div class="w-full rounded-2xl px-4 py-3 flex items-center gap-3" style="background-color: rgba(0,0,0,0.15);">
                <p class="text-xs flex-1 truncate font-mono" style="color: rgba(255,255,255,0.6);">{{ $registerUrl }}</p>
                <button
                    onclick="navigator.clipboard.writeText('{{ $registerUrl }}').then(() => { this.textContent = '✓'; setTimeout(() => this.textContent = 'Copiar', 1500); })"
                    class="shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-opacity hover:opacity-80 text-white"
                    style="background-color: rgba(255,255,255,0.2);">
                    Copiar
                </button>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex gap-3 w-full pb-6">
            <a href="{{ route('admin.parties.edit', $party) }}" wire:navigate
                class="flex-1 font-semibold py-4 rounded-2xl text-center transition-opacity hover:opacity-80 text-sm"
                style="background-color: rgba(255,255,255,0.2); color: white;">
                Editar fiesta
            </a>
            <a href="{{ $qrImageUrl }}" target="_blank" download
                class="flex-1 font-bold py-4 rounded-2xl text-center transition-opacity hover:opacity-90 text-sm text-white"
                style="background-color: #2d0a3e;">
                Descargar QR
            </a>
        </div>

    </div>

</div>
@fluxScripts
</body>
</html>
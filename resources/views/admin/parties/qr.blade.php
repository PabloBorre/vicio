{{-- resources/views/admin/parties/qr.blade.php --}}
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
    <title>QR — {{ $party->name }}</title>
</head>
<body class="min-h-screen bg-zinc-950 flex flex-col px-6 py-6 text-white">

    @php
        $registerUrl = route('party.register', $party->qr_code);
        $qrImageUrl  = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&data=' . urlencode($registerUrl);
    @endphp

    {{-- Botón volver arriba a la izquierda --}}
    <div class="mb-6">
        <a
            href="{{ route('admin.parties.index') }}"
            wire:navigate
            class="inline-flex items-center gap-2 text-zinc-400 hover:text-white text-sm font-medium transition-colors"
        >
            ← Volver al listado
        </a>
    </div>

    {{-- Contenido centrado --}}
    <div class="flex flex-col items-center justify-center flex-1">

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold">{{ $party->name }}</h1>
            @if($party->location)
                <p class="text-zinc-400 text-sm mt-1">{{ $party->location }}</p>
            @endif
            @if($party->starts_at)
                <p class="text-zinc-500 text-xs mt-1">{{ $party->starts_at->format('d/m/Y · H:i') }}</p>
            @endif
        </div>

        {{-- QR --}}
        <div class="bg-white p-4 rounded-3xl shadow-2xl mb-8">
            <img
                src="{{ $qrImageUrl }}"
                alt="QR de {{ $party->name }}"
                class="size-64 block"
            />
        </div>

        {{-- URL del enlace --}}
        <div class="w-full max-w-sm bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-3 flex items-center gap-3 mb-6">
            <p class="flex-1 text-zinc-400 text-xs truncate font-mono">{{ $registerUrl }}</p>
            <button
                onclick="navigator.clipboard.writeText('{{ $registerUrl }}').then(() => { this.innerText = '✓ Copiado'; setTimeout(() => this.innerText = 'Copiar', 1500) })"
                class="shrink-0 text-xs font-semibold text-vicio-300 hover:text-vicio-200 transition-colors"
            >
                Copiar
            </button>
        </div>

        {{-- Botones de acción --}}
        <div class="flex flex-col gap-3 w-full max-w-sm">
            <button
                onclick="if(navigator.share){ navigator.share({ title: '{{ addslashes($party->name) }}', text: 'Únete a la fiesta', url: '{{ $registerUrl }}' }) }"
                class="w-full vicio-gradient text-white font-semibold py-4 rounded-2xl hover:opacity-90 transition-opacity"
            >
                Compartir enlace
            </button>

            <a
                href="{{ $qrImageUrl }}&format=png"
                download="qr-{{ Str::slug($party->name) }}.png"
                class="w-full bg-zinc-900 border border-zinc-800 text-white font-semibold py-4 rounded-2xl text-center hover:border-zinc-700 transition-colors"
            >
                Descargar QR
            </a>
        </div>

    </div>

    @fluxScripts
</body>
</html>
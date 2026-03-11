<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-950 flex flex-col items-center justify-center px-6 py-10 text-white">

    <div class="w-full max-w-sm flex flex-col items-center gap-6 text-center">

        @if($party->cover_image)
            <img src="{{ asset('storage/' . $party->cover_image) }}"
                 class="w-full h-48 object-cover rounded-3xl" />
        @else
            <div class="size-20 rounded-full vicio-gradient flex items-center justify-center shadow-xl">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-10 fill-white">
                    <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
                </svg>
            </div>
        @endif

        <div>
            <h1 class="text-2xl font-bold">{{ $party->name }}</h1>
            @if($party->location)
                <p class="text-zinc-400 text-sm mt-1">{{ $party->location }}</p>
            @endif
            @if($party->starts_at)
                <p class="text-zinc-500 text-xs mt-1">{{ $party->starts_at->format('d/m/Y · H:i') }}</p>
            @endif
        </div>

        @if($party->description)
            <p class="text-zinc-400 text-sm leading-relaxed">{{ $party->description }}</p>
        @endif

        @php
    $statusConfig = [
        'draft'        => ['text' => 'Próximamente',      'class' => 'bg-zinc-800 text-zinc-400'],
        'registration' => ['text' => 'Registro abierto',  'class' => 'bg-green-900/50 text-green-400'],
        'countdown'    => ['text' => 'Registro abierto',  'class' => 'bg-green-900/50 text-green-400'],  // mismo que registration
        'active'       => ['text' => '¡En marcha!',       'class' => 'bg-vicio-900/50 text-vicio-300'],
        'finished'     => ['text' => 'Finalizada',        'class' => 'bg-zinc-800 text-zinc-500'],
    ];
    $config = $statusConfig[$party->status] ?? $statusConfig['draft'];
@endphp

        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
            {{ $config['text'] }}
        </span>

        <p class="text-zinc-500 text-sm">
            {{ $party->users()->count() }} personas ya se han unido
        </p>

        {{-- Botón de acción --}}
<div class="w-full max-w-sm space-y-3">
    @if($party->status === 'finished')
        <p class="text-center text-zinc-500 text-sm">Esta fiesta ha finalizado.</p>
    @elseif(in_array($party->status, ['registration', 'countdown', 'active']))
        
            href="{{ route('party.register', $party->qr_code) }}"
            class="w-full vicio-gradient text-white font-semibold text-center py-4 rounded-2xl block hover:opacity-90 transition-opacity text-base"
        >
            🎉 ¡Quiero entrar!
        </a>

        @auth
            <p class="text-center text-zinc-600 text-xs">
                Entrando como <span class="text-zinc-400">{{ auth()->user()->username ?? auth()->user()->name }}</span>
            </p>
        @endauth
    @else
        <p class="text-center text-zinc-500 text-sm">El registro no está abierto todavía.</p>
    @endif
</div>

    </div>

    @fluxScripts
</body>
</html>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-950">

<div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">

    {{-- Cover image o gradiente --}}
    <div class="w-full max-w-sm mb-6">
        @if($party->cover_image)
            <img
                src="{{ asset('storage/' . $party->cover_image) }}"
                class="w-full h-48 object-cover rounded-2xl"
                alt="{{ $party->name }}"
            />
        @else
            <div class="w-full h-48 vicio-gradient rounded-2xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-16 fill-white opacity-50" viewBox="0 0 24 24">
                    <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
                </svg>
            </div>
        @endif
    </div>

    {{-- Info de la fiesta --}}
    <div class="w-full max-w-sm space-y-4 text-center mb-8">
        <div>
            <h1 class="text-white text-2xl font-bold">{{ $party->name }}</h1>
            @if($party->description)
                <p class="text-zinc-400 text-sm mt-2">{{ $party->description }}</p>
            @endif
        </div>

        <div class="flex items-center justify-center gap-6 text-sm">
            @if($party->location)
                <div class="flex items-center gap-1.5 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ $party->location }}</span>
                </div>
            @endif
            <div class="flex items-center gap-1.5 text-zinc-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $party->starts_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        {{-- Badge de estado --}}
        <div class="flex justify-center">
            @php
                $statusConfig = [
                    'draft'        => ['text' => 'Próximamente', 'class' => 'bg-zinc-800 text-zinc-400'],
                    'registration' => ['text' => 'Registro abierto', 'class' => 'bg-green-900/50 text-green-400'],
                    'countdown'    => ['text' => 'Empieza pronto', 'class' => 'bg-yellow-900/50 text-yellow-400'],
                    'active'       => ['text' => '¡En marcha!', 'class' => 'bg-vicio-900/50 text-vicio-300'],
                    'finished'     => ['text' => 'Finalizada', 'class' => 'bg-zinc-800 text-zinc-500'],
                ];
                $config = $statusConfig[$party->status] ?? $statusConfig['draft'];
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
                {{ $config['text'] }}
            </span>
        </div>

        {{-- Nº de participantes --}}
        <p class="text-zinc-500 text-sm">
            {{ $party->users()->count() }} personas ya se han unido
        </p>
    </div>

    {{-- Botón de acción --}}
    <div class="w-full max-w-sm space-y-3">
        @if($party->status === 'finished')
            <p class="text-center text-zinc-500 text-sm">Esta fiesta ha finalizado.</p>
        @else
            <a
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
        @endif
    </div>

</div>

@fluxScripts
</body>
</html>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiesta finalizada — Vicio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-950 min-h-screen flex flex-col items-center justify-center px-4 py-10">

    <div class="flex flex-col items-center gap-6 text-center max-w-xs w-full">

        {{-- Icono --}}
        <div class="size-20 rounded-full bg-zinc-800 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>

        {{-- Texto --}}
        <div class="space-y-2">
            <h1 class="text-white font-bold text-2xl">Fiesta finalizada</h1>
            <p class="text-zinc-500 text-sm leading-relaxed">
                <span class="text-zinc-300 font-medium">{{ $party->name }}</span>
                ya ha terminado. ¡Esperamos que lo hayas pasado genial!
            </p>
        </div>

        {{-- CTA si está autenticado --}}
        @auth
            <a href="{{ route('dashboard') }}"
               class="w-full vicio-gradient text-white font-semibold py-4 rounded-2xl text-center hover:opacity-90 transition-opacity">
                Ir al inicio
            </a>
            <a href="{{ route('chats.index') }}"
               class="w-full bg-zinc-800 text-zinc-300 font-medium py-3 rounded-2xl text-center hover:bg-zinc-700 transition-colors text-sm">
                Ver mis chats
            </a>
        @else
            <a href="{{ route('home') }}"
               class="w-full vicio-gradient text-white font-semibold py-4 rounded-2xl text-center hover:opacity-90 transition-opacity">
                Volver al inicio
            </a>
        @endauth

    </div>

</body>
</html>
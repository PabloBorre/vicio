<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VicioApp — El Tinder de las Fiestas</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen vicio-gradient flex flex-col items-center justify-center px-6 text-white">

    {{-- Logo + nombre --}}
    <div class="flex flex-col items-center gap-4 mb-10">
        <div class="size-20 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-10 fill-white">
                <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
            </svg>
        </div>
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight">VicioApp</h1>
            <p class="text-vicio-200 mt-1 text-lg">El Tinder de las fiestas</p>
        </div>
    </div>

    {{-- Descripción --}}
    <p class="text-center text-white/70 text-sm max-w-xs mb-10 leading-relaxed">
        Escanea el QR de tu fiesta, conéctate con quienes están allí y descubre quien te gusta esta noche.
    </p>

    {{-- Botones de acción --}}
    @auth
        <a
            href="{{ route('dashboard') }}"
            class="w-full max-w-xs bg-white text-vicio-500 font-semibold text-center py-3.5 rounded-2xl shadow-lg hover:bg-vicio-50 transition-colors"
        >
            Ir al inicio
        </a>
    @else
        <div class="flex flex-col gap-3 w-full max-w-xs">
            <a
                href="{{ route('login') }}"
                class="w-full bg-white text-vicio-500 font-semibold text-center py-3.5 rounded-2xl shadow-lg hover:bg-vicio-50 transition-colors"
            >
                Iniciar sesión
            </a>
            @if (Route::has('register'))
                <a
                    href="{{ route('register') }}"
                    class="w-full bg-white/10 backdrop-blur-sm border border-white/30 text-white font-semibold text-center py-3.5 rounded-2xl hover:bg-white/20 transition-colors"
                >
                    Crear cuenta
                </a>
            @endif
        </div>
    @endauth

    {{-- Footer --}}
    <p class="mt-12 text-white/30 text-xs">© {{ date('Y') }} VicioApp. Todos los derechos reservados.</p>

</body>
</html>
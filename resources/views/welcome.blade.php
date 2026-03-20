<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen antialiased text-white" style="background-color: #A678C8 !important">
        <div class="flex flex-col items-center justify-between min-h-screen px-6" style="padding-top: 80px; padding-bottom: 56px;">

        {{-- Texto superior --}}
        <p class="text-center text-zinc-400 text-sm leading-relaxed max-w-xs">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore
        </p>

        {{-- Botones --}}
        <div class="w-full max-w-sm">
            @auth
                
                <a    href="{{ route('dashboard') }}"
                    class="block w-full text-[#1a0a2e] font-bold text-2xl text-center transition-colors hover:opacity-90"
                    style="background-color: #7B4FA6; padding: 48px 24px; border-radius: 9999px;"
                >
                    Ir al inicio
                </a>
            @else
                {{-- Iniciar sesión --}}
                
                <a    href="{{ route('login') }}"
                    class="block w-full text-[#1a0a2e] font-bold text-2xl text-center transition-colors hover:opacity-90 relative"
                    style="background-color: #7B4FA6; padding: 48px 24px; border-radius: 9999px; z-index: 10;"
                >
                    Iniciar sesión
                </a>

                {{-- Crear cuenta (solapado) --}}
                @if (Route::has('register'))
                    
                    <a    href="{{ route('register') }}"
                        class="block w-full text-[#1a0a2e] font-bold text-2xl text-center transition-colors hover:opacity-90 relative"
                        style="background-color: #D4A8D4; padding: 48px 24px; border-radius: 9999px; margin-top: -20px; z-index: 0;"
                    >
                        Crear cuenta
                    </a>
                @endif
            @endauth
        </div>

                {{-- Logo --}}
        <div class="flex flex-col items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="VicioApp" width="150" height="150">
            <span class="text-white font-bold text-2xl tracking-tight">VicioApp</span>
        </div>

        {{-- Texto inferior --}}
        <p class="text-center text-zinc-400 text-sm leading-relaxed max-w-xs">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit
        </p>

    </div>

    @fluxScripts
</body>
</html>
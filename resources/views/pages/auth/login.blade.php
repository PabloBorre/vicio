<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen antialiased" style="background-color: #A678C8 !important; max-width: 430px; margin:auto"">
    <div class="flex flex-col items-center justify-between min-h-screen px-6" style="padding-top: 80px; padding-bottom: 56px;">

        {{-- Logo arriba --}}
        <div class="flex flex-col items-center gap-3">
            <img src="{{ asset('images/Logo.png') }}" alt="VicioApp" width="70" height="70">
            <span class="text-white font-bold text-2xl tracking-tight">VicioApp</span>
        </div>

        {{-- Formulario --}}
        <div class="w-full max-w-sm flex flex-col gap-4">

            <x-auth-session-status class="text-center text-purple-100 text-sm" :status="session('status')" />

            @if(request('banned'))
                <div class="rounded-2xl px-4 py-3 text-sm text-center text-red-200" style="background-color: #2D1B4E;">
                    Tu cuenta ha sido suspendida. Contacta con el administrador.
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
                @csrf

                {{-- Email --}}
                <div>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Tu email"
                        required
                        autofocus
                        autocomplete="email"
                        style="background-color: #2D1B4E; border:none; border-radius:9999px; font-size:16px; padding:18px 24px; color:white; outline:none; width:100%;"
                    />
                    @error('email')
                        <p class="text-red-200 text-xs mt-1 px-4">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div>
                    <input
                        type="password"
                        name="password"
                        placeholder="Contraseña"
                        required
                        autocomplete="current-password"
                        style="background-color: #2D1B4E; border:none; border-radius:9999px; font-size:16px; padding:18px 24px; color:white; outline:none; width:100%;"
                    />
                    @error('password')
                        <p class="text-red-200 text-xs mt-1 px-4">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Recuérdame + olvidaste contraseña --}}
                <div class="flex items-center justify-between px-2">
                    <label class="flex items-center gap-2 text-sm text-purple-100 cursor-pointer">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Recuérdame
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-purple-100 underline">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                {{-- Botón entrar --}}
                <button
                    type="submit"
                    style="background-color: #2D1B4E; border:none; border-radius:9999px; padding:20px 24px; font-size:20px; font-weight:700; color:white; cursor:pointer; width:100%;"
                >
                    Iniciar sesión
                </button>

            </form>

            {{-- Link registro --}}
            @if (Route::has('register'))
                <p class="text-center text-sm text-purple-100">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="font-bold underline text-white">Crear cuenta</a>
                </p>
            @endif

        </div>

        {{-- Texto inferior --}}
        <p class="text-center text-purple-100 text-xs opacity-60">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit
        </p>

    </div>

    @fluxScripts
</body>
</html>
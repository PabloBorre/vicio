<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
    <title>Únete a {{ $party->name }}</title>
</head>
<body class="min-h-screen bg-zinc-950 text-white">

<div class="min-h-screen flex flex-col items-center justify-start px-5 py-8 pb-16">

    {{-- Header fiesta --}}
    <div class="w-full max-w-sm mb-6 text-center">
        @if($party->cover_image)
            <img src="{{ asset('storage/' . $party->cover_image) }}"
                 class="w-full h-36 object-cover rounded-2xl mb-4" />
        @endif
        <h1 class="text-xl font-bold">Únete a <span class="text-vicio-400">{{ $party->name }}</span></h1>
        <p class="text-zinc-500 text-sm mt-1">Entra con tu cuenta o crea una nueva</p>
    </div>

    {{-- Tabs --}}
    @php $tab = session('tab', old('tab', 'login')); @endphp

    <div class="w-full max-w-sm">
        <div class="flex bg-zinc-900 rounded-2xl p-1 mb-6" x-data="{ tab: '{{ $tab }}' }">
            <button
                @click="tab = 'login'"
                :class="tab === 'login' ? 'bg-zinc-700 text-white' : 'text-zinc-400 hover:text-zinc-200'"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
            >
                Iniciar sesión
            </button>
            <button
                @click="tab = 'register'"
                :class="tab === 'register' ? 'bg-zinc-700 text-white' : 'text-zinc-400 hover:text-zinc-200'"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
            >
                Crear cuenta
            </button>
        </div>

        {{-- Errores --}}
        @if($errors->any())
            <div class="bg-red-900/30 border border-red-800 rounded-xl px-4 py-3 mb-4">
                <ul class="text-red-400 text-xs space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- TAB LOGIN --}}
        <div x-show="tab === 'login'" x-cloak>
            <form method="POST" action="{{ route('party.login', $party->qr_code) }}" class="flex flex-col gap-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-300">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="email@ejemplo.com"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 text-sm" />
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-300">Contraseña</label>
                    <input type="password" name="password" required
                        placeholder="Tu contraseña"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 text-sm" />
                </div>
                <button type="submit"
                    class="w-full vicio-gradient text-white font-semibold py-4 rounded-2xl hover:opacity-90 transition-opacity mt-2">
                    Entrar a la fiesta 🎉
                </button>
            </form>
        </div>

        {{-- TAB CREAR CUENTA --}}
        <div x-show="tab === 'register'" x-cloak>
            {{-- Aquí pega el formulario de registro actual que ya tienes --}}
            <form method="POST" action="{{ route('party.store', $party->qr_code) }}"
                  enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                {{-- ... todos los campos actuales del form de registro ... --}}
                <button type="submit"
                    class="w-full vicio-gradient text-white font-semibold py-4 rounded-2xl hover:opacity-90 transition-opacity mt-2">
                    Crear cuenta y entrar 🚀
                </button>
            </form>
        </div>
    </div>
</div>

@fluxScripts
</body>
</html>
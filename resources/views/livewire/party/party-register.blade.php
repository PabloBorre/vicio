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

    @php $tab = $errors->any() ? old('_tab', 'login') : 'login'; @endphp

    <div class="w-full max-w-sm">
        <div class="flex bg-zinc-900 rounded-2xl p-1 mb-6" x-data="{ tab: '{{ $tab }}' }">
            <button
                @click="tab = 'login'"
                :class="tab === 'login' ? 'bg-zinc-700 text-white' : 'text-zinc-400 hover:text-zinc-200'"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all">
                Iniciar sesión
            </button>
            <button
                @click="tab = 'register'"
                :class="tab === 'register' ? 'bg-zinc-700 text-white' : 'text-zinc-400 hover:text-zinc-200'"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all">
                Crear cuenta
            </button>
        </div>

        {{-- Errores globales --}}
        @if($errors->any())
            <div class="bg-red-900/30 border border-red-800 rounded-xl px-4 py-3 mb-4">
                <ul class="text-red-400 text-xs space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── TAB LOGIN ── --}}
        <div x-show="tab === 'login'" x-cloak>
            <form method="POST" action="{{ route('party.login', $party->qr_code) }}" class="flex flex-col gap-4">
                @csrf
                <input type="hidden" name="_tab" value="login">
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

        {{-- ── TAB REGISTRO ── --}}
        <div x-show="tab === 'register'" x-cloak>
            <form method="POST" action="{{ route('party.store', $party->qr_code) }}"
                  enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                <input type="hidden" name="_tab" value="register">

                {{-- FOTO DE PERFIL --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-300">Foto de perfil *</label>
                    <div class="flex flex-col items-center gap-3">
                        <div class="relative w-24 h-24 rounded-full overflow-hidden bg-zinc-800 border-2 border-zinc-700 flex items-center justify-center"
                            x-data="{ preview: null }">
                            <img x-show="preview" :src="preview" class="w-full h-full object-cover" />
                            <svg x-show="!preview" class="w-10 h-10 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <label class="absolute inset-0 cursor-pointer flex items-end justify-center pb-2">
                                <span class="bg-black/60 text-white text-xs px-2 py-0.5 rounded-full">Cambiar</span>
                                <input type="file" name="profile_photo" accept="image/*" class="hidden"
                                    x-on:change="
                                        const file = $event.target.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = e => preview = e.target.result;
                                            reader.readAsDataURL(file);
                                        }
                                    " />
                            </label>
                        </div>
                    </div>
                    @error('profile_photo')
                        <p class="text-red-400 text-xs text-center">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NOMBRE COMPLETO --}}
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-300">Nombre completo *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Tu nombre"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 text-sm" />
                    @error('name') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- USERNAME --}}
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-300">Nombre de usuario *</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        placeholder="Solo letras, números y _"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 text-sm" />
                    @error('username') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- EMAIL --}}
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-300">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="tu@email.com"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 text-sm" />
                    @error('email') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- CONTRASEÑA --}}
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-300">Contraseña *</label>
                    <input type="password" name="password" required
                        placeholder="Mínimo 8 caracteres"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 text-sm" />
                    @error('password') <p class="text-white/45 text-xs">Mínimo 8 caracteres</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-300">Confirmar contraseña *</label>
                    <input type="password" name="password_confirmation" required
                        placeholder="Repite la contraseña"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 text-sm" />
                </div>

                {{-- EDAD --}}
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-300">Edad * <span class="text-zinc-500 text-xs font-normal">(mínimo 18)</span></label>
                    <input type="number" name="age" value="{{ old('age') }}" min="18" max="99" required
                        placeholder="Tu edad"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 text-sm" />
                    @error('age') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- GÉNERO --}}
                    <div class="space-y-2" x-data="optionGroup('gender_identity', @js(old('gender_identity')))">
                        <label class="text-sm font-medium text-zinc-300">Me identifico como *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="select('man')"
                            :class="value === 'man' ? activeClass : inactiveClass"
                            class="flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200">
                            <span class="text-2xl">👨</span>
                            <span>Hombre</span>
                        </button>
                        <button type="button" @click="select('woman')"
                            :class="value === 'woman' ? activeClass : inactiveClass"
                            class="flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200">
                            <span class="text-2xl">👩</span>
                            <span>Mujer</span>
                        </button>
                    </div>
                    <input type="hidden" name="gender_identity" :value="value" />
                    @error('gender_identity') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                </div>

{{-- PREFERENCIA SEXUAL --}}
<div class="space-y-2" x-data="optionGroup('sexual_preference', @js(old('sexual_preference')))">
                    <label class="text-sm font-medium text-zinc-300">Me gustan *</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" @click="select('man')"
                            :class="value === 'hetero' ? activeClass : inactiveClass"
                            class="flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200">
                            <span class="text-2xl">♂</span>
                            <span class="text-xs text-center leading-tight">Hombres</span>
                        </button>
                        <button type="button" @click="select('woman')"
                            :class="value === 'homo' ? activeClass : inactiveClass"
                            class="flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200">
                            <span class="text-2xl">♀</span>
                            <span class="text-xs text-center leading-tight">Mujeres</span>
                        </button>
                        <button type="button" @click="select('both')"
                            :class="value === 'bi' ? activeClass : inactiveClass"
                            class="flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200">
                            <span class="text-2xl">⚥</span>
                            <span class="text-xs text-center leading-tight">Ambos</span>
                        </button>
                    </div>
                    <input type="hidden" name="sexual_preference" :value="value" />
                    @error('sexual_preference') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- BIO --}}
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-300">Sobre ti * <span class="text-zinc-500 text-xs font-normal">(mínimo 10 caracteres)</span></label>
                    <textarea name="bio" rows="3" required minlength="10" maxlength="500"
                        placeholder="Cuéntanos algo de ti..."
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors resize-none text-sm"
                    >{{ old('bio') }}</textarea>
                    @error('bio') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    class="w-full vicio-gradient text-white font-semibold py-4 rounded-2xl hover:opacity-90 transition-opacity mt-2">
                    Crear cuenta y entrar 🚀
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function optionGroup(name, initial) {
    return {
        value: initial || '',
        activeClass:   'bg-vicio-600 text-white border-vicio-500',
        inactiveClass: 'bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500 hover:text-zinc-300',
        select(val) { this.value = val; }
    }
}
</script>

@fluxScripts
</body>
</html>
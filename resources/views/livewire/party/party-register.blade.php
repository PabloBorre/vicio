<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
    <title>Únete a {{ $party->name }}</title>
</head>
<body class="min-h-screen bg-zinc-950 text-white">

<div class="min-h-screen flex flex-col items-center justify-start px-5 py-8 pb-16">

    {{-- Header de la fiesta --}}
    <div class="w-full max-w-sm mb-6 text-center">
        @if($party->cover_image)
            <img src="{{ asset('storage/' . $party->cover_image) }}"
                 class="w-full h-36 object-cover rounded-2xl mb-4" />
        @else
            <div class="size-16 rounded-2xl vicio-gradient flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-8 fill-white">
                    <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10z"/>
                </svg>
            </div>
        @endif
        <h1 class="text-xl font-bold">Únete a <span class="text-vicio-400">{{ $party->name }}</span></h1>
        <p class="text-zinc-500 text-sm mt-1">Crea tu cuenta para entrar a la fiesta</p>
    </div>

    {{-- Formulario --}}
    <form
        method="POST"
        action="{{ route('party.store', $party->qr_code) }}"
        enctype="multipart/form-data"
        class="w-full max-w-sm flex flex-col gap-4"
    >
        @csrf

        {{-- Errores globales --}}
        @if($errors->any())
            <div class="bg-red-900/30 border border-red-800 rounded-xl px-4 py-3">
                <p class="text-red-400 text-sm font-medium mb-1">Por favor corrige los siguientes errores:</p>
                <ul class="text-red-400 text-xs space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── FOTO DE PERFIL ── --}}
        <div class="flex flex-col items-center gap-2">
            <label for="profile_photo" class="cursor-pointer group">
                <div class="size-24 rounded-full overflow-hidden bg-zinc-800 border-2 border-zinc-700 group-hover:border-vicio-400 transition-colors flex items-center justify-center" id="photo-preview-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-zinc-500" id="photo-placeholder" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <img id="photo-preview" src="" class="w-full h-full object-cover hidden" />
                </div>
                <p class="text-vicio-400 text-xs text-center mt-1">Foto de perfil <span class="text-red-400">*</span></p>
            </label>
            <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="hidden"
                onchange="
                    const file = this.files[0];
                    if(file){
                        const reader = new FileReader();
                        reader.onload = e => {
                            document.getElementById('photo-preview').src = e.target.result;
                            document.getElementById('photo-preview').classList.remove('hidden');
                            document.getElementById('photo-placeholder').classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                "
            />
            @error('profile_photo')
                <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── NOMBRE COMPLETO ── --}}
        <div class="space-y-1.5">
            <label class="text-sm font-medium text-zinc-300">Nombre completo <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Tu nombre" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm @error('name') border-red-500 @enderror"/>
            @error('name')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- ── USERNAME ── --}}
        <div class="space-y-1.5">
            <label class="text-sm font-medium text-zinc-300">Nombre de usuario <span class="text-red-400">*</span></label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm">@</span>
                <input type="text" name="username" value="{{ old('username') }}"
                    required
                    placeholder="tuusuario"
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-xl pl-7 pr-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm @error('username') border-red-500 @enderror"
                />
            </div>
            @error('username')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- ── EMAIL ── --}}
        <div class="space-y-1.5">
            <label class="text-sm font-medium text-zinc-300">Email <span class="text-red-400">*</span></label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                placeholder="email@ejemplo.com"
                autocomplete="email"
                class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm @error('email') border-red-500 @enderror"
            />
            @error('email')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- ── CONTRASEÑA ── --}}
        <div class="space-y-1.5">
            <label class="text-sm font-medium text-zinc-300">Contraseña <span class="text-red-400">*</span></label>
            <input
                type="password"
                name="password"
                required
                placeholder="Mínimo 8 caracteres"
                autocomplete="new-password"
                class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm @error('password') border-red-500 @enderror"
            />
            @error('password')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-1.5">
            <label class="text-sm font-medium text-zinc-300">Confirmar contraseña <span class="text-red-400">*</span></label>
            <input
                type="password"
                name="password_confirmation"
                required
                placeholder="Repite la contraseña"
                autocomplete="new-password"
                class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm"
            />
        </div>

        {{-- ── EDAD ── --}}
        <div class="space-y-1.5">
            <label class="text-sm font-medium text-zinc-300">Edad <span class="text-red-400">*</span></label>
            <input
                type="number"
                name="age"
                value="{{ old('age') }}"
                required
                min="18"
                max="99"
                placeholder="Tu edad (mínimo 18)"
                class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm @error('age') border-red-500 @enderror"
            />
            @error('age')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- ── GÉNERO ── --}}
        <div class="space-y-2">
            <label class="text-sm font-medium text-zinc-300">Me identifico como <span class="text-red-400">*</span></label>
            <div class="grid grid-cols-2 gap-2">
                @foreach(['man' => 'Hombre', 'woman' => 'Mujer', 'non_binary' => 'No binario', 'other' => 'Otro'] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="gender_identity" value="{{ $value }}"
                            {{ old('gender_identity') === $value ? 'checked' : '' }} class="sr-only peer" required />
                        <div class="py-2.5 rounded-xl text-sm font-medium text-center border transition-all bg-zinc-900 text-zinc-400 border-zinc-700 peer-checked:vicio-gradient peer-checked:text-white peer-checked:border-transparent hover:border-zinc-500">
                            {{ $label }}
                        </div>
                    </label>
                @endforeach
            </div>
            @error('gender_identity')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- ── PREFERENCIA SEXUAL ── --}}
        <div class="space-y-2">
            <label class="text-sm font-medium text-zinc-300">Me interesan <span class="text-red-400">*</span></label>
            <div class="grid grid-cols-2 gap-2">
                @foreach(['hetero' => 'El género opuesto', 'homo' => 'Mi mismo género', 'bi' => 'Hombres y mujeres', 'pan' => 'Todos los géneros'] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="sexual_preference" value="{{ $value }}"
                            {{ old('sexual_preference') === $value ? 'checked' : '' }} class="sr-only peer" required />
                        <div class="py-2.5 px-2 rounded-xl text-xs font-medium text-center border transition-all bg-zinc-900 text-zinc-400 border-zinc-700 peer-checked:vicio-gradient peer-checked:text-white peer-checked:border-transparent hover:border-zinc-500 leading-tight">
                            {{ $label }}
                        </div>
                    </label>
                @endforeach
            </div>
            @error('sexual_preference')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- ── BIO ── --}}
        <div class="space-y-1.5">
            <label class="text-sm font-medium text-zinc-300">Sobre ti <span class="text-red-400">*</span></label>
            <textarea
                name="bio"
                rows="3"
                required
                minlength="10"
                maxlength="500"
                placeholder="Cuéntanos algo de ti... (mínimo 10 caracteres)"
                class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors resize-none text-sm @error('bio') border-red-500 @enderror"
            >{{ old('bio') }}</textarea>
            @error('bio')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- ── SUBMIT ── --}}
        <button type="submit" class="w-full vicio-gradient text-white font-bold py-4 rounded-2xl hover:opacity-90 transition-opacity text-base mt-2">¡Crear cuenta y entrar!</button>

        <p class="text-center text-xs text-zinc-600">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-vicio-400 hover:text-vicio-300">Iniciar sesión</a>
        </p>

    </form>
</div>

@fluxScripts
</body>
</html>
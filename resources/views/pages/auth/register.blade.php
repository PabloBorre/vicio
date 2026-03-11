<x-layouts::auth :title="__('Crear cuenta')">
    <div class="flex flex-col gap-5">
        <x-auth-header :title="__('Crea tu cuenta')" :description="__('Completa todos los campos para registrarte')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        {{-- Errores globales --}}
        @if($errors->any())
            <div class="bg-red-900/30 border border-red-800 rounded-xl px-4 py-3">
                <p class="text-red-400 text-sm font-medium mb-1">Corrige los siguientes errores:</p>
                <ul class="text-red-400 text-xs space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf

            {{-- ── FOTO DE PERFIL ── --}}
            <div class="flex flex-col items-center gap-1">
                <label for="profile_photo" class="cursor-pointer group">
                    <div class="size-24 rounded-full overflow-hidden bg-zinc-800 border-2 border-zinc-700 group-hover:border-vicio-400 transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-zinc-500" id="photo-placeholder" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <img id="photo-preview" src="" class="w-full h-full object-cover hidden" />
                    </div>
                    <p class="text-vicio-400 text-xs text-center mt-1">Foto de perfil *</p>
                </label>
                <input
                    id="profile_photo"
                    name="profile_photo"
                    type="file"
                    accept="image/*"
                    class="hidden"
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
            <flux:input
                name="name"
                :label="__('Nombre completo *')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Tu nombre completo"
            />

            {{-- ── USERNAME ── --}}
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-zinc-300">Nombre de usuario *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm z-10">@</span>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        placeholder="tuusuario"
                        class="w-full bg-zinc-800 dark:bg-zinc-800 border border-zinc-600 rounded-lg pl-7 pr-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm"
                    />
                </div>
                @error('username')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- ── EMAIL ── --}}
            <flux:input
                name="email"
                :label="__('Email *')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@ejemplo.com"
            />

            {{-- ── CONTRASEÑA ── --}}
            <flux:input
                name="password"
                :label="__('Contraseña *')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Mínimo 8 caracteres"
                viewable
            />

            <flux:input
                name="password_confirmation"
                :label="__('Confirmar contraseña *')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Repite la contraseña"
                viewable
            />

            {{-- ── EDAD ── --}}
            <flux:input
                name="age"
                :label="__('Edad * (mínimo 18)')"
                :value="old('age')"
                type="number"
                min="18"
                max="99"
                required
                placeholder="Tu edad"
            />

{{-- Género --}}
<div class="space-y-2">
    <label class="text-sm font-medium text-zinc-300">Me identifico como *</label>
    <div class="grid grid-cols-2 gap-3">
        @foreach(['man' => ['label' => 'Hombre', 'icon' => '👨'], 'woman' => ['label' => 'Mujer', 'icon' => '👩']] as $value => $opt)
            <label class="cursor-pointer flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200
                bg-zinc-900 text-zinc-400 border-zinc-700
                has-[:checked]:bg-vicio-600 has-[:checked]:text-white has-[:checked]:border-vicio-500
                hover:border-zinc-500 hover:text-zinc-300">
                <input type="button" name="gender_identity" value="{{ $value }}"
                    {{ old('gender_identity') === $value ? 'checked' : '' }}
                    class="hidden" required />
                <span class="text-2xl">{{ $opt['icon'] }}</span>
                <span>{{ $opt['label'] }}</span>
            </label>
        @endforeach
    </div>
    @error('gender_identity')
        <p class="text-red-400 text-xs">{{ $message }}</p>
    @enderror
</div>

{{-- Preferencia --}}
<div class="space-y-2">
    <label class="text-sm font-medium text-zinc-300">Qué busco *</label>
    <div class="grid grid-cols-3 gap-3">
        @foreach(['man' => ['label' => 'Hombre', 'icon' => '👨'], 'woman' => ['label' => 'Mujer', 'icon' => '👩'], 'bi' => ['label' => 'Ambos', 'icon' => '💞']] as $value => $opt)
            <label class="cursor-pointer flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200
                bg-zinc-900 text-zinc-400 border-zinc-700
                has-[:checked]:bg-vicio-600 has-[:checked]:text-white has-[:checked]:border-vicio-500
                hover:border-zinc-500 hover:text-zinc-300">
                <input type="button" name="sexual_preference" value="{{ $value }}"
                    {{ old('sexual_preference') === $value ? 'checked' : '' }}
                    class="hidden" required />
                <span class="text-2xl">{{ $opt['icon'] }}</span>
                <span>{{ $opt['label'] }}</span>
            </label>
        @endforeach
    </div>
    @error('sexual_preference')
        <p class="text-red-400 text-xs">{{ $message }}</p>
    @enderror
</div>

            {{-- ── BIO ── --}}
            <div class="space-y-1.5">
                <label class="text-sm font-medium text-zinc-300">Sobre ti * <span class="text-zinc-500 font-normal text-xs">(mínimo 10 caracteres)</span></label>
                <textarea
                    name="bio"
                    rows="3"
                    required
                    minlength="10"
                    maxlength="500"
                    placeholder="Cuéntanos algo de ti..."
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors resize-none text-sm"
                >{{ old('bio') }}</textarea>
                @error('bio')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <flux:button type="submit" variant="primary" class="w-full mt-1">
                {{ __('Crear cuenta') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('¿Ya tienes cuenta?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Iniciar sesión') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
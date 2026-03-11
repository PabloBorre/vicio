<x-layouts::auth :title="__('Crear cuenta')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Crea tu cuenta')" :description="__('Completa todos los campos para registrarte')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf

            {{-- Foto de perfil --}}
            <div class="flex flex-col items-center gap-2">
                <label for="profile_photo" class="cursor-pointer group">
                    <div class="size-24 rounded-full overflow-hidden bg-zinc-800 border-2 border-zinc-700 group-hover:border-vicio-400 transition-colors flex items-center justify-center" id="photo-preview-wrapper">
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
                        if (file) {
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

            {{-- Nombre completo --}}
            <flux:input
                name="name"
                :label="__('Nombre completo')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Tu nombre real"
            />

            {{-- Username --}}
            <flux:input
                name="username"
                :label="__('Nombre en la app')"
                :value="old('username')"
                type="text"
                required
                placeholder="Cómo te verán los demás"
            />

            {{-- Email --}}
            <flux:input
                name="email"
                :label="__('Email')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@ejemplo.com"
            />

            {{-- Password --}}
            <flux:input
                name="password"
                :label="__('Contraseña')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Mínimo 8 caracteres"
                viewable
            />

            <flux:input
                name="password_confirmation"
                :label="__('Confirmar contraseña')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Repite la contraseña"
                viewable
            />

            {{-- Edad --}}
            <flux:input
                name="age"
                :label="__('Edad')"
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
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['hombre' => 'Hombre', 'mujer' => 'Mujer', 'otro' => 'Otro'] as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="gender_identity" value="{{ $value }}" {{ old('gender_identity') === $value ? 'checked' : '' }} class="sr-only peer" />
                            <div class="py-2.5 rounded-xl text-sm font-medium text-center border transition-all bg-zinc-900 text-zinc-400 border-zinc-700 peer-checked:vicio-gradient peer-checked:text-white peer-checked:border-transparent hover:border-zinc-500">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('gender_identity')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- Preferencia --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-zinc-300">Me interesan *</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['hombres' => 'Hombres', 'mujeres' => 'Mujeres', 'todos' => 'Todos'] as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="sexual_preference" value="{{ $value }}" {{ old('sexual_preference') === $value ? 'checked' : '' }} class="sr-only peer" />
                            <div class="py-2.5 rounded-xl text-sm font-medium text-center border transition-all bg-zinc-900 text-zinc-400 border-zinc-700 peer-checked:vicio-gradient peer-checked:text-white peer-checked:border-transparent hover:border-zinc-500">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('sexual_preference')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bio --}}
            <div class="space-y-1.5">
                <label class="text-sm font-medium text-zinc-300">Sobre ti *</label>
                <textarea
                    name="bio"
                    rows="3"
                    placeholder="Cuéntanos algo de ti..."
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors resize-none text-sm"
                >{{ old('bio') }}</textarea>
                @error('bio')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Crear cuenta') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('¿Ya tienes cuenta?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Iniciar sesión') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Crear cuenta')" :description="__('Únete a VicioApp')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf

            {{-- ── FOTO DE PERFIL ── --}}
            <div class="flex flex-col items-center gap-2">
                <label for="profile_photo" class="cursor-pointer group">
                    <div class="size-24 rounded-full bg-zinc-800 border-2 border-zinc-700 group-hover:border-vicio-500 transition-colors overflow-hidden flex items-center justify-center relative"
                         id="photo-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <img id="photo-preview" src="" class="w-full h-full object-cover hidden absolute inset-0" />
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
                                document.getElementById('photo-placeholder').querySelector('svg').classList.add('hidden');
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
                placeholder="Tu nombre"
            />

            {{-- ── USERNAME ── --}}
            <flux:input
                name="username"
                :label="__('Nombre de usuario *')"
                :value="old('username')"
                type="text"
                required
                autocomplete="username"
                placeholder="Solo letras, números y _"
            />

            {{-- ── EMAIL ── --}}
            <flux:input
                name="email"
                :label="__('Email *')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="tu@email.com"
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

            {{-- ── GÉNERO ── --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-zinc-300">Me identifico como *</label>
                <div class="grid grid-cols-2 gap-3" id="gender-group">
                    <button type="button"
                        data-group="gender_identity" data-value="man"
                        onclick="selectOption(this)"
                        class="option-btn flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500 hover:text-zinc-300">
                        <span class="text-2xl">👨</span>
                        <span>Hombre</span>
                    </button>
                    <button type="button"
                        data-group="gender_identity" data-value="woman"
                        onclick="selectOption(this)"
                        class="option-btn flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500 hover:text-zinc-300">
                        <span class="text-2xl">👩</span>
                        <span>Mujer</span>
                    </button>
                </div>
                <input type="hidden" name="gender_identity" id="gender_identity" value="{{ old('gender_identity') }}" required />
                @error('gender_identity')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

{{-- ── PREFERENCIA SEXUAL ── --}}
<div class="space-y-2">
    <label class="text-sm font-medium text-zinc-300">Me gustan *</label>
    <div class="grid grid-cols-3 gap-3" id="preference-group">
        <button type="button"
            data-group="sexual_preference" data-value="hetero"
            onclick="selectOption(this)"
            class="option-btn flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500 hover:text-zinc-300">
            <span class="text-2xl">♂</span>
            <span class="text-xs text-center leading-tight">Hombres</span>
        </button>
        <button type="button"
            data-group="sexual_preference" data-value="homo"
            onclick="selectOption(this)"
            class="option-btn flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500 hover:text-zinc-300">
            <span class="text-2xl">♀</span>
            <span class="text-xs text-center leading-tight">Mujeres</span>
        </button>
        <button type="button"
            data-group="sexual_preference" data-value="bi"
            onclick="selectOption(this)"
            class="option-btn flex flex-col items-center gap-1.5 py-4 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500 hover:text-zinc-300">
            <span class="text-2xl">⚥</span>
            <span class="text-xs text-center leading-tight">Ambos</span>
        </button>
    </div>
    <input type="hidden" name="sexual_preference" id="sexual_preference" value="{{ old('sexual_preference') }}" required />
    @error('sexual_preference')
        <p class="text-red-400 text-xs">{{ $message }}</p>
    @enderror
</div>

            <script>
                const ACTIVE = ['bg-vicio-600', 'text-white', 'border-vicio-500'];
                const INACTIVE = ['bg-zinc-900', 'text-zinc-400', 'border-zinc-700'];

                function selectOption(btn) {
                    const group = btn.dataset.group;
                    // Desmarcar todos los botones del mismo grupo
                    document.querySelectorAll(`[data-group="${group}"]`).forEach(b => {
                        b.classList.remove(...ACTIVE);
                        b.classList.add(...INACTIVE);
                    });
                    // Marcar el clickado
                    btn.classList.remove(...INACTIVE);
                    btn.classList.add(...ACTIVE);
                    // Guardar valor en el hidden input
                    document.getElementById(group).value = btn.dataset.value;
                }

                // Restaurar selección si hay old() values (tras error de validación)
                document.addEventListener('DOMContentLoaded', () => {
                    ['gender_identity', 'sexual_preference'].forEach(group => {
                        const val = document.getElementById(group).value;
                        if (val) {
                            const btn = document.querySelector(`[data-group="${group}"][data-value="${val}"]`);
                            if (btn) selectOption(btn);
                        }
                    });
                });
            </script>

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
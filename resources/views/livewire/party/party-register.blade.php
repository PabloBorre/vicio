<div class="min-h-screen bg-zinc-950 flex flex-col items-center justify-center px-4 py-10">

    {{-- Header con logo --}}
    <div class="flex flex-col items-center gap-2 mb-8">
        <div class="size-12 rounded-full vicio-gradient flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-6 fill-white">
                <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
            </svg>
        </div>
        <h1 class="text-white font-bold text-xl">Únete a {{ $party->name }}</h1>
        <p class="text-zinc-500 text-sm">Completa tu perfil para entrar</p>
    </div>

    {{-- Indicador de pasos --}}
    <div class="flex items-center gap-2 mb-8">
        @for($i = 1; $i <= $totalSteps; $i++)
            <div class="flex items-center gap-2">
                <div @class([
                    'size-8 rounded-full flex items-center justify-center text-sm font-bold transition-all',
                    'vicio-gradient text-white' => $step >= $i,
                    'bg-zinc-800 text-zinc-500' => $step < $i,
                ])>
                    @if($step > $i)
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @else
                        {{ $i }}
                    @endif
                </div>
                @if($i < $totalSteps)
                    <div @class([
                        'w-8 h-px transition-all',
                        'bg-vicio-400' => $step > $i,
                        'bg-zinc-700' => $step <= $i,
                    ])></div>
                @endif
            </div>
        @endfor
    </div>

    {{-- Formulario --}}
    <div class="w-full max-w-sm">

        {{-- PASO 1: Username y foto --}}
        @if($step === 1)
            <div class="space-y-6">
                <div class="text-center">
                    <h2 class="text-white font-semibold text-lg">Tu perfil</h2>
                    <p class="text-zinc-500 text-sm mt-1">¿Cómo quieres que te vean esta noche?</p>
                </div>

                {{-- Foto de perfil --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="relative">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}"
                                 class="size-24 rounded-full object-cover border-2 border-vicio-400" />
                        @else
                            <div class="size-24 rounded-full bg-zinc-800 border-2 border-zinc-700 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <label for="photo-upload"
                               class="absolute -bottom-1 -right-1 size-8 rounded-full bg-vicio flex items-center justify-center cursor-pointer hover:bg-vicio-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </label>
                        <input id="photo-upload" type="file" wire:model="photo" accept="image/*" class="hidden" />
                    </div>
                    @error('photo')
                        <p class="text-red-400 text-xs">{{ $message }}</p>
                    @enderror
                    <p class="text-zinc-500 text-xs">Toca para añadir una foto</p>
                </div>

                {{-- Username --}}
                <div class="space-y-1">
                    <label class="text-zinc-300 text-sm font-medium">Nombre de usuario</label>
                    <input
                        wire:model="username"
                        type="text"
                        placeholder="@tunombre"
                        maxlength="30"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors"
                    />
                    @error('username')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    wire:click="nextStep"
                    class="w-full vicio-gradient text-white font-semibold py-3.5 rounded-xl hover:opacity-90 transition-opacity"
                >
                    Continuar →
                </button>
            </div>
        @endif

        {{-- PASO 2: Edad, identidad y preferencia --}}
        @if($step === 2)
            <div class="space-y-6">
                <div class="text-center">
                    <h2 class="text-white font-semibold text-lg">Cuéntanos más</h2>
                    <p class="text-zinc-500 text-sm mt-1">Para mostrarte los perfiles correctos</p>
                </div>

                {{-- Edad --}}
                <div class="space-y-1">
                    <label class="text-zinc-300 text-sm font-medium">Edad</label>
                    <input
                        wire:model="age"
                        type="number"
                        min="18"
                        max="99"
                        placeholder="Tu edad"
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors"
                    />
                    @error('age')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Identidad de género --}}
                <div class="space-y-2">
                    <label class="text-zinc-300 text-sm font-medium">Me identifico como</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach([
                            'man'        => 'Hombre',
                            'woman'      => 'Mujer',
                            'non_binary' => 'No binario',
                            'other'      => 'Otro',
                        ] as $value => $label)
                            <button
                                wire:click="$set('gender_identity', '{{ $value }}')"
                                type="button"
                                @class([
                                    'py-3 px-4 rounded-xl border text-sm font-medium transition-all',
                                    'border-vicio-400 bg-vicio-900/30 text-vicio-300' => $gender_identity === $value,
                                    'border-zinc-700 bg-zinc-900 text-zinc-400 hover:border-zinc-600' => $gender_identity !== $value,
                                ])
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    @error('gender_identity')
                        <p class="text-red-400 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Preferencia sexual --}}
                <div class="space-y-2">
                    <label class="text-zinc-300 text-sm font-medium">Me interesan</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach([
                            'hetero' => 'Personas del sexo opuesto',
                            'homo'   => 'Personas de mi mismo sexo',
                            'bi'     => 'Ambos sexos',
                            'pan'    => 'Todas las identidades',
                        ] as $value => $label)
                            <button
                                wire:click="$set('sexual_preference', '{{ $value }}')"
                                type="button"
                                @class([
                                    'py-3 px-4 rounded-xl border text-sm font-medium transition-all text-left',
                                    'border-vicio-400 bg-vicio-900/30 text-vicio-300' => $sexual_preference === $value,
                                    'border-zinc-700 bg-zinc-900 text-zinc-400 hover:border-zinc-600' => $sexual_preference !== $value,
                                ])
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    @error('sexual_preference')
                        <p class="text-red-400 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button
                        wire:click="prevStep"
                        class="flex-1 bg-zinc-800 text-zinc-300 font-semibold py-3.5 rounded-xl hover:bg-zinc-700 transition-colors"
                    >
                        ← Atrás
                    </button>
                    <button
                        wire:click="nextStep"
                        class="flex-1 vicio-gradient text-white font-semibold py-3.5 rounded-xl hover:opacity-90 transition-opacity"
                    >
                        Continuar →
                    </button>
                </div>
            </div>
        @endif

        {{-- PASO 3: Bio --}}
        @if($step === 3)
            <div class="space-y-6">
                <div class="text-center">
                    <h2 class="text-white font-semibold text-lg">Tu descripción</h2>
                    <p class="text-zinc-500 text-sm mt-1">Cuéntales algo sobre ti (obligatorio)</p>
                </div>

                <div class="space-y-1">
                    <label class="text-zinc-300 text-sm font-medium">Bio</label>
                    <textarea
                        wire:model="bio"
                        rows="5"
                        maxlength="500"
                        placeholder="¿Qué te hace especial esta noche? Cuéntalo aquí..."
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors resize-none"
                    ></textarea>
                    <div class="flex justify-between">
                        @error('bio')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @else
                            <span></span>
                        @enderror
                        <span class="text-zinc-600 text-xs">{{ strlen($bio) }}/500</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        wire:click="prevStep"
                        class="flex-1 bg-zinc-800 text-zinc-300 font-semibold py-3.5 rounded-xl hover:bg-zinc-700 transition-colors"
                    >
                        ← Atrás
                    </button>
                    <button
                        wire:click="register"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="flex-1 vicio-gradient text-white font-semibold py-3.5 rounded-xl hover:opacity-90 transition-opacity"
                    >
                        <span wire:loading.remove wire:target="register">¡Entrar a la fiesta! 🎉</span>
                        <span wire:loading wire:target="register">Guardando...</span>
                    </button>
                </div>
            </div>
        @endif

    </div>
</div>
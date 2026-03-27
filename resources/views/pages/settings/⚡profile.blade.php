<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Support\ImageHelper;
use Livewire\WithFileUploads;

new #[Title('Editar perfil')] class extends Component {
    use WithFileUploads;

    public string $name              = '';
    public string $username          = '';
    public string $email             = '';
    public string $bio               = '';
    public string|int $age           = '';
    public string $sexual_preference = '';
    public $photo = null;

    public string $current_password      = '';
    public string $password              = '';
    public string $password_confirmation = '';

    public function boot(): void
    {
        // Solo carga si las propiedades están vacías (primera vez)
        if ($this->name === '') {
            $user = Auth::user();
            $this->name              = $user->name              ?? '';
            $this->username          = $user->username          ?? '';
            $this->email             = $user->email             ?? '';
            $this->bio               = $user->bio               ?? '';
            $this->age               = (string) ($user->age    ?? '');
            $this->sexual_preference = $user->sexual_preference ?? '';
        }
    }

    public function setPref(string $value): void
    {
        $this->sexual_preference = $value;
    }

    public function updateProfile(): void
    {
        $user = Auth::user();

        if ($this->current_password || $this->password || $this->password_confirmation) {
            if (!Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'La contraseña actual no es correcta.');
                return;
            }

            $this->validate([
                'current_password'      => ['required'],
                'password'              => ['required', 'string', 'min:8', 'confirmed'],
                'password_confirmation' => ['required'],
            ], [
                'current_password.required' => 'Introduce tu contraseña actual.',
                'password.min'              => 'La nueva contraseña debe tener al menos 8 caracteres.',
                'password.confirmed'        => 'Las contraseñas no coinciden.',
            ]);
        }

        $validated = $this->validate([
            'name'              => ['required', 'string', 'max:255'],
            'username'          => ['required', 'string', 'min:3', 'max:30', 'unique:users,username,' . $user->id, 'regex:/^[a-zA-Z0-9_]+$/'],
            'email'             => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio'               => ['nullable', 'string', 'max:500'],
            'age'               => ['required', 'integer', 'min:18', 'max:99'],
            'sexual_preference' => ['required', 'in:man,woman,both'],
            'photo'             => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'username.regex'  => 'Solo letras, números y guiones bajos.',
            'email.unique'    => 'Este email ya está registrado.',
            'age.min'         => 'Debes tener al menos 18 años.',
        ]);

        if ($this->password) {
            $user->password = Hash::make($this->password);
        }

        if ($this->photo) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $validated['profile_photo_path'] = ImageHelper::storeAsWebP($this->photo);
        }

        unset($validated['photo']);

        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->fill($validated);
        $user->save();

        $this->photo = null;
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';

        session()->flash('profile-updated', true);
        $this->redirect(route('profile.edit'), navigate: false);
    }
}; ?>

<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body style="margin:0; padding:0; background-color: #A678C8;">

<div class="w-full max-w-[430px] mx-auto flex flex-col" style="min-height: 100dvh; background-color: #A678C8;">

    {{-- ── HEADER ── --}}
    @php
        $currentParty = auth()->user()->parties()
            ->whereIn('parties.status', ['registration', 'active'])
            ->latest('pivot_joined_at')
            ->first();
        $backRoute = $currentParty
            ? ($currentParty->status === 'active'
                ? route('party.swipe', $currentParty->qr_code)
                : route('party.waiting', $currentParty->qr_code))
            : route('parties');
    @endphp

    <div class="relative z-50 shrink-0 flex items-center justify-between px-4 py-3" style="background-color: #A678C8;">

        {{-- Flecha atrás (izquierda) --}}
        <a href="{{ $backRoute }}" wire:navigate
           class="size-11 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        {{-- Logo centro --}}
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/Logo.png') }}" alt="VicioApp" width="48" height="48">
            <span class="text-white font-bold text-2xl tracking-tight">VicioApp</span>
        </div>

        {{-- Botón menú (derecha) --}}
        <div class="relative" x-data="{ open: false }">
            <button
                @click.stop="open = !open"
                class="size-11 rounded-full bg-white flex items-center justify-center hover:bg-white/90 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="#49197C">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Overlay oscuro --}}
            <div
                x-show="open"
                @click="open = false"
                class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm"
                style="display: none;"
            ></div>

            {{-- Dropdown --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                class="absolute right-0 top-14 z-50 flex flex-col gap-2 min-w-[200px]"
                style="display: none;"
            >
                <a href="{{ route('chats.index') }}"
                    wire:navigate
                    class="w-full text-center px-6 py-4 rounded-2xl font-semibold text-lg whitespace-nowrap shadow-lg transition-colors"
                    style="background: #f5f0eb; color: #49197C;"
                >
                    Mis chats
                </a>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button
                        type="submit"
                        class="w-full text-center px-6 py-4 rounded-2xl font-semibold text-lg whitespace-nowrap shadow-lg transition-colors"
                        style="background: #f5f0eb; color: #49197C;"
                    >
                        Salir ✕
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── CONTENIDO ── --}}
    <div class="flex-1 overflow-y-auto">
        <div class="px-5 pb-16 flex flex-col gap-4 pt-6">

            @php
                $userId  = auth()->id();
                $matches = \App\Models\PartyMatch::where('user1_id', $userId)->orWhere('user2_id', $userId)->count();
                $unread  = \App\Models\Message::whereHas('match', fn($q) => $q->where('user1_id', $userId)->orWhere('user2_id', $userId))
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();
                $likes   = \App\Models\Swipe::where('swiped_id', $userId)->where('direction', 'like')->count();
            @endphp

            {{-- Foto de perfil --}}
            <div class="flex flex-col items-center gap-4">

                {{-- Avatar con selector de foto --}}
                <label for="photo-input" class="cursor-pointer relative" style="width: 120px; height: 120px;">
                    <div class="size-full rounded-full overflow-hidden" style="outline: 3px solid rgba(255,255,255,0.5); outline-offset: 3px;">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover" alt="Nueva foto">
                        @else
                            <img src="{{ auth()->user()->profile_photo_url }}" class="w-full h-full object-cover" alt="{{ auth()->user()->username }}">
                        @endif
                    </div>
                    {{-- Icono editar --}}
                    <div class="absolute bottom-0 right-0 size-9 rounded-full flex items-center justify-center shadow-lg"
                         style="background-color: #49197C; border: 2px solid #A678C8;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <input id="photo-input" type="file" wire:model="photo" accept="image/*" class="hidden">
                </label>

                @error('photo') <p class="text-red-200 text-xs text-center -mt-2">{{ $message }}</p> @enderror

                {{-- Stats: mismas burbujas solapadas que los gustos --}}
<div class="flex justify-center items-center w-full" style="padding: 4px 0;">

    {{-- Matches --}}
    <div class="rounded-full flex flex-col items-center justify-center gap-0.5 font-semibold"
        style="width:110px; height:110px; position:relative; z-index:3; margin-right:-22px; background-color: #C8A8DC; color: #2D0A4E; flex-shrink:0;">
        <span class="text-2xl font-bold">{{ $matches }}</span>
        <span class="text-xs font-medium">Matches</span>
    </div>

    {{-- Sin leer (centro, más grande y oscuro) --}}
    <div class="rounded-full flex flex-col items-center justify-center gap-0.5 font-semibold"
        style="width:120px; height:120px; position:relative; z-index:2; background-color: #DCC8EC; color: #2D0A4E; flex-shrink:0;">
        <span class="text-2xl font-bold">{{ $unread }}</span>
        <span class="text-xs font-medium">Sin leer</span>
    </div>

    {{-- Likes --}}
    <div class="rounded-full flex flex-col items-center justify-center gap-0.5 font-semibold"
        style="width:110px; height:110px; position:relative; z-index:3; margin-left:-22px; background-color: #E8C8F0; color: #2D0A4E; flex-shrink:0;">
        <span class="text-2xl font-bold">{{ $likes }}</span>
        <span class="text-xs font-medium">Likes</span>
    </div>

</div>
            </div>

            {{-- Mensaje de guardado --}}
            @if(session('profile-updated'))
                <div class="text-center text-white text-sm font-semibold py-1">✓ Cambios guardados</div>
            @endif

            {{-- Formulario --}}
            <form wire:submit.prevent="updateProfile" class="flex flex-col gap-4">

                {{-- CAMPOS DE TEXTO --}}
                <div>
                    <input type="text" wire:model="name" placeholder="Tu nombre de viciosx"
                        class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                        style="background-color: #2D0A4E; font-size: 16px; padding: 18px 24px; border-radius: 9999px;"/>
                    @error('name') <p class="text-red-200 text-xs text-center mt-1 px-4">{{ $message }}</p> @enderror
                </div>

                <div>
                    <input type="text" wire:model="username" placeholder="Nombre de usuario"
                        class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                        style="background-color: #2D0A4E; font-size: 16px; padding: 18px 24px; border-radius: 9999px;"/>
                    @error('username') <p class="text-red-200 text-xs text-center mt-1 px-4">{{ $message }}</p> @enderror
                </div>

                <div>
                    <input type="email" wire:model="email" placeholder="Tu email"
                        class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                        style="background-color: #2D0A4E; font-size: 16px; padding: 18px 24px; border-radius: 9999px;"/>
                    @error('email') <p class="text-red-200 text-xs text-center mt-1 px-4">{{ $message }}</p> @enderror
                </div>

                <div>
                    <input type="number" wire:model="age" placeholder="Edad, pero sin mentir" min="18" max="99"
                        class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                        style="background-color: #2D0A4E; font-size: 16px; padding: 18px 24px; border-radius: 9999px;"/>
                    @error('age') <p class="text-red-200 text-xs text-center mt-1 px-4">{{ $message }}</p> @enderror
                </div>

                <div>
                    <textarea wire:model="bio" placeholder="Cuéntanos algo de ti, pero tómatelo en serio, más que en tu bio de Insta"
                        rows="3"
                        class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30 resize-none"
                        style="background-color: #2D0A4E; font-size: 16px; padding: 18px 24px; border-radius: 28px;">{{ $bio }}</textarea>
                    @error('bio') <p class="text-red-200 text-xs text-center mt-1 px-4">{{ $message }}</p> @enderror
                </div>

                {{-- PREFERENCIA SEXUAL — burbujas solapadas --}}
                <p class="text-white/70 text-sm text-center" style="padding-top: 8px;">Me gustan:</p>

                <div x-data="{ pref: '{{ $sexual_preference }}' }"
                     x-init="$watch('pref', v => $wire.sexual_preference = v)"
                     class="flex justify-center items-center" style="padding: 8px 0;">

                    <button type="button" @click.prevent="pref = 'man'"
                        class="rounded-full font-semibold text-sm transition-all duration-200"
                        style="width:110px; height:110px; position:relative; z-index:3; margin-right:-22px; border:none; cursor:pointer;"
                        :style="{ backgroundColor: pref === 'man' ? '#5B1A9E' : '#C8A8DC', color: pref === 'man' ? 'white' : '#2D0A4E' }">
                        Hombres
                    </button>
                    <button type="button" @click.prevent="pref = 'both'"
                        class="rounded-full font-semibold text-sm transition-all duration-200"
                        style="width:120px; height:120px; position:relative; z-index:2; border:none; cursor:pointer;"
                        :style="{ backgroundColor: pref === 'both' ? '#5B1A9E' : '#DCC8EC', color: pref === 'both' ? 'white' : '#2D0A4E' }">
                        Ambos
                    </button>
                    <button type="button" @click.prevent="pref = 'woman'"
                        class="rounded-full font-semibold text-sm transition-all duration-200"
                        style="width:110px; height:110px; position:relative; z-index:3; margin-left:-22px; border:none; cursor:pointer;"
                        :style="{ backgroundColor: pref === 'woman' ? '#5B1A9E' : '#E8C8F0', color: pref === 'woman' ? 'white' : '#2D0A4E' }">
                        Mujeres
                    </button>
                </div>
                @error('sexual_preference') <p class="text-red-200 text-xs text-center -mt-2">{{ $message }}</p> @enderror

                {{-- CAMBIO DE CONTRASEÑA --}}
                <div class="pt-2 flex flex-col gap-3">
                    <p class="text-white/70 text-sm text-center">Cambiar contraseña (opcional)</p>

                    <input type="password" wire:model="current_password" placeholder="Contraseña actual"
                        class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                        style="background-color: #2D0A4E; font-size: 16px; padding: 18px 24px; border-radius: 9999px;"/>
                    @error('current_password') <p class="text-red-200 text-xs text-center -mt-2 px-4">{{ $message }}</p> @enderror

                    <input type="password" wire:model="password" placeholder="Nueva contraseña"
                        class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                        style="background-color: #2D0A4E; font-size: 16px; padding: 18px 24px; border-radius: 9999px;"/>
                    @error('password') <p class="text-red-200 text-xs text-center -mt-2 px-4">{{ $message }}</p> @enderror

                    <input type="password" wire:model="password_confirmation" placeholder="Repite la nueva contraseña"
                        class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                        style="background-color: #2D0A4E; font-size: 16px; padding: 18px 24px; border-radius: 9999px;"/>
                </div>

                {{-- BOTÓN GUARDAR --}}
                <button type="submit"
                    class="w-full font-bold text-xl transition-opacity hover:opacity-90 mt-2"
                    style="background-color: #2D0A4E; color: white; padding: 22px 24px; border-radius: 9999px; border: none; cursor: pointer;">
                    Guardar cambios
                </button>

            </form>

        </div>
    </div>

</div>

@fluxScripts
</body>
</html>
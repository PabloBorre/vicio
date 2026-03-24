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

    public function mount(): void
    {
        $user = Auth::user();
        $this->name              = $user->name              ?? '';
        $this->username          = $user->username          ?? '';
        $this->email             = $user->email             ?? '';
        $this->bio               = $user->bio               ?? '';
        $this->age               = (string) ($user->age    ?? '');
        $this->sexual_preference = $user->sexual_preference ?? '';
    }

    public function updateProfile(): void
    {
        $user = Auth::user();

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

        // Contraseña (opcional)
        if ($this->current_password || $this->password) {
            $this->validate([
                'current_password'      => ['required'],
                'password'              => ['required', 'min:8', 'confirmed'],
                'password_confirmation' => ['required'],
            ], [
                'current_password.required' => 'Introduce tu contraseña actual.',
                'password.min'              => 'La nueva contraseña debe tener al menos 8 caracteres.',
                'password.confirmed'        => 'Las contraseñas no coinciden.',
            ]);

            if (!Hash::check($this->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'La contraseña actual no es correcta.',
                ]);
            }

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

        // Elimina el dispatch y añade redirect
        session()->flash('profile-updated', true);
        $this->redirect(route('profile.edit'), navigate: false);
    }
}; ?>

<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body style="margin:0; padding:0; min-height:100dvh; background-color: #A678C8;">

<div class="w-full max-w-[430px] mx-auto" style="min-height:100dvh; background-color: #A678C8; padding: 0 20px 50px 20px;">

    {{-- Header --}}
    <div class="flex items-center justify-between pt-6 pb-4">
        <a href="{{ route('dashboard') }}" wire:navigate
           class="size-10 rounded-full flex items-center justify-center"
           style="background: rgba(255,255,255,0.25);">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="text-white font-bold text-xl tracking-tight">VicioApp</span>
    </div>

<form wire:submit.prevent="updateProfile"
      x-data
      @submit="
          let g = document.querySelector('[name=_gender_identity]');
          let p = document.querySelector('[name=_sexual_preference]');
          if (g) $wire.set('gender_identity', g.value, false);
          if (p) $wire.set('sexual_preference', p.value, false);
      "
      class="space-y-3">
        {{-- FOTO con texto curvo --}}
        <div class="flex justify-center py-6">
            <div class="relative" style="width: 180px; height: 180px;">
                <svg viewBox="0 0 170 170" class="absolute inset-0 w-full h-full" style="z-index:2; pointer-events:none;">
                    <defs>
                        <path id="circle-text" d="M 85,85 m -74,0 a 74,74 0 1,1 148,0 a 74,74 0 1,1 -148,0"/>
                    </defs>
                    <text font-size="12" font-weight="600" fill="white" font-family="sans-serif" letter-spacing="1.5">
                        <textPath href="#circle-text" startOffset="2%">
                            La foto más viciosa de tu galería, pero sin pasarte
                        </textPath>
                    </text>
                </svg>
                <label for="photo-upload" class="cursor-pointer block" style="position:absolute; inset:18px; z-index:1;">
                    <div class="w-full h-full rounded-full overflow-hidden" style="border: 4px solid rgba(255,255,255,0.7);">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover"/>
                        @else
                            <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover"/>
                        @endif
                    </div>
                </label>
                <input id="photo-upload" type="file" wire:model="photo" accept="image/*" class="hidden"/>
            </div>
        </div>

        @error('photo') <p class="text-red-200 text-xs text-center -mt-2">{{ $message }}</p> @enderror

        @if(session('profile-updated'))
    <div class="text-center text-white text-sm font-semibold py-1">✓ Cambios guardados</div>
@endif

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

{{-- PREFERENCIA SEXUAL --}}
<p class="text-white/70 text-sm text-center" style="padding-top: 8px;">Me gustan:</p>

<div x-data="{ pref: '{{ $sexual_preference }}' }" class="flex justify-center items-center" style="padding: 8px 0;">
    <input type="hidden" name="_sexual_preference" x-bind:value="pref" />
    <button type="button" @click="pref = 'man'"
        class="rounded-full font-semibold text-sm transition-all duration-200"
        style="width:110px; height:110px; position:relative; z-index:3; margin-right:-22px; border:none; cursor:pointer;"
        :style="{ backgroundColor: pref === 'man' ? '#5B1A9E' : '#C8A8DC', color: pref === 'man' ? 'white' : '#2D0A4E' }">
        Hombres
    </button>
    <button type="button" @click="pref = 'both'"
        class="rounded-full font-semibold text-sm transition-all duration-200"
        style="width:120px; height:120px; position:relative; z-index:2; border:none; cursor:pointer;"
        :style="{ backgroundColor: pref === 'both' ? '#5B1A9E' : '#DCC8EC', color: pref === 'both' ? 'white' : '#2D0A4E' }">
        Ambos
    </button>
    <button type="button" @click="pref = 'woman'"
        class="rounded-full font-semibold text-sm transition-all duration-200"
        style="width:110px; height:110px; position:relative; z-index:3; margin-left:-22px; border:none; cursor:pointer;"
        :style="{ backgroundColor: pref === 'woman' ? '#5B1A9E' : '#E8C8F0', color: pref === 'woman' ? 'white' : '#2D0A4E' }">
        Mujeres
    </button>
</div>
        @error('sexual_preference') <p class="text-red-200 text-xs text-center mt-1 px-4">{{ $message }}</p> @enderror

{{-- CONTRASEÑA --}}
<p class="text-white/70 text-sm text-center" style="padding-top: 8px;">Cambiar contraseña</p>

<div x-data="{ show1: false, show2: false, show3: false }" class="space-y-3">
    <div>
        <div class="relative">
            <input :type="show1 ? 'text' : 'password'" wire:model="current_password"
                placeholder="Contraseña actual"
                class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                style="background-color: #2D0A4E; font-size: 16px; padding: 18px 50px 18px 24px; border-radius: 9999px; display: block; width: 100%; box-sizing: border-box;"/>
            <button type="button" @click="show1 = !show1"
                style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 0; color: rgba(255,255,255,0.5);">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:22px; height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        @error('current_password') <p class="text-red-200 text-xs text-center mt-1 px-4">{{ $message }}</p> @enderror
    </div>

    <div>
        <div class="relative">
            <input :type="show2 ? 'text' : 'password'" wire:model="password"
                placeholder="Nueva contraseña"
                class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                style="background-color: #2D0A4E; font-size: 16px; padding: 18px 50px 18px 24px; border-radius: 9999px; display: block; width: 100%; box-sizing: border-box;"/>
            <button type="button" @click="show2 = !show2"
                style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 0; color: rgba(255,255,255,0.5);">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:22px; height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        @error('password') <p class="text-red-200 text-xs text-center mt-1 px-4">{{ $message }}</p> @enderror
    </div>

    <div>
        <div class="relative">
            <input :type="show3 ? 'text' : 'password'" wire:model="password_confirmation"
                placeholder="Confirmar contraseña"
                class="w-full text-white text-center font-medium placeholder-white/60 border-0 focus:outline-none focus:ring-2 focus:ring-white/30"
                style="background-color: #2D0A4E; font-size: 16px; padding: 18px 50px 18px 24px; border-radius: 9999px; display: block; width: 100%; box-sizing: border-box;"/>
            <button type="button" @click="show3 = !show3"
                style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 0; color: rgba(255,255,255,0.5);">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:22px; height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

        {{-- BOTÓN --}}
        <div style="padding-top: 16px;">
            <button type="submit"
                class="w-full font-bold text-xl transition-opacity hover:opacity-90"
                style="background-color: #F0E6D3; color: #2D0A4E; padding: 20px 24px; border-radius: 9999px; border: none;">
                Guardar cambios
            </button>
        </div>

    </form>
</div>

@fluxScripts
</body>
</html>
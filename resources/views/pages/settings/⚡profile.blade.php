<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Ajustes de perfil')] class extends Component {
    use WithFileUploads;

    public string $name      = '';
    public string $username  = '';
    public string $email     = '';
    public string $bio       = '';
    public string $age       = '';
    public string $gender_identity   = '';
    public string $sexual_preference = '';
    public $photo = null; // nuevo archivo subido

    public function mount(): void
    {
        $user = Auth::user();
        $this->name             = $user->name     ?? '';
        $this->username         = $user->username ?? '';
        $this->email            = $user->email    ?? '';
        $this->bio              = $user->bio       ?? '';
        $this->age              = $user->age       ?? '';
        $this->gender_identity  = $user->gender_identity   ?? '';
        $this->sexual_preference = $user->sexual_preference ?? '';
    }

    public function updateProfile(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name'             => ['required', 'string', 'max:255'],
            'username'         => ['required', 'string', 'min:3', 'max:30', 'unique:users,username,' . $user->id, 'regex:/^[a-zA-Z0-9_]+$/'],
            'email'            => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio'              => ['nullable', 'string', 'min:10', 'max:500'],
            'age'              => ['required', 'integer', 'min:18', 'max:99'],
            'gender_identity'  => ['required', 'in:man,woman,non_binary,other'],
            'sexual_preference'=> ['required', 'in:hetero,homo,bi,pan'],
            'photo'            => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'username.unique'  => 'Este nombre de usuario ya está en uso.',
            'username.regex'   => 'Solo letras, números y guiones bajos.',
            'email.unique'     => 'Este email ya está registrado.',
            'bio.min'          => 'La bio debe tener al menos 10 caracteres.',
            'age.min'          => 'Debes tener al menos 18 años.',
        ]);

        if ($this->photo) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $validated['profile_photo_path'] = $this->photo->store('profile-photos', 'public');
            unset($validated['photo']);
        } else {
            unset($validated['photo']);
        }

        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->fill($validated);
        $user->save();

        $this->photo = null;
        $this->dispatch('profile-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout
        :heading="__('Perfil')"
        :subheading="__('Actualiza tu información personal')"
    >
        <form wire:submit="updateProfile" class="my-6 w-full space-y-5">

            {{-- ── FOTO DE PERFIL ── --}}
            <div class="flex flex-col items-start gap-3">
                <label class="text-sm font-medium text-zinc-300">Foto de perfil</label>
                <div class="flex items-center gap-4">
                    <div class="size-20 rounded-full overflow-hidden bg-zinc-800 border border-zinc-700 shrink-0">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover" />
                        @else
                            <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover" />
                        @endif
                    </div>
                    <div>
                        <label for="photo-upload" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium rounded-xl border border-zinc-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Cambiar foto
                        </label>
                        <input id="photo-upload" type="file" wire:model="photo" accept="image/*" class="hidden" />
                        <p class="text-zinc-600 text-xs mt-1.5">JPG, PNG o WebP · Máx 5 MB</p>
                    </div>
                </div>
                @error('photo') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- ── NOMBRE ── --}}
            <flux:input wire:model="name" label="Nombre completo" type="text" required autocomplete="name" />

            {{-- ── USERNAME ── --}}
            <flux:input wire:model="username" label="Nombre de usuario" type="text" required
                        description="Solo letras, números y guiones bajos" />

            {{-- ── EMAIL ── --}}
            <flux:input wire:model="email" label="Email" type="email" required autocomplete="email" />

            {{-- ── EDAD ── --}}
            <flux:input wire:model="age" label="Edad" type="number" min="18" max="99" required />

            {{-- ── GÉNERO ── --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-zinc-300">Me identifico como</label>
                <div class="grid grid-cols-2 gap-3" id="settings-gender-group">
                    @foreach(['man' => ['label'=>'Hombre','icon'=>'👨'], 'woman' => ['label'=>'Mujer','icon'=>'👩']] as $val => $opt)
                        <button type="button"
                            data-group="gender_identity" data-value="{{ $val }}"
                            onclick="settingsSelect(this)"
                            class="settings-option-btn flex flex-col items-center gap-1.5 py-3 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500">
                            <span class="text-xl">{{ $opt['icon'] }}</span>
                            <span>{{ $opt['label'] }}</span>
                        </button>
                    @endforeach
                </div>
                <input type="hidden" id="settings_gender_identity" wire:model="gender_identity" />
                @error('gender_identity') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- ── PREFERENCIA SEXUAL ── --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-zinc-300">Me gustan</label>
                <div class="grid grid-cols-3 gap-3" id="settings-preference-group">
                    <button type="button" data-group="sexual_preference" data-value="hetero"
                        onclick="settingsSelect(this)"
                        class="settings-option-btn flex flex-col items-center gap-1.5 py-3 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500">
                        <span class="text-xl">🔥</span>
                        <span class="text-center leading-tight text-xs">El género opuesto</span>
                    </button>
                    <button type="button" data-group="sexual_preference" data-value="homo"
                        onclick="settingsSelect(this)"
                        class="settings-option-btn flex flex-col items-center gap-1.5 py-3 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500">
                        <span class="text-xl">✨</span>
                        <span class="text-center leading-tight text-xs">Mi género</span>
                    </button>
                    <button type="button" data-group="sexual_preference" data-value="bi"
                        onclick="settingsSelect(this)"
                        class="settings-option-btn flex flex-col items-center gap-1.5 py-3 rounded-2xl text-sm font-semibold border-2 transition-all duration-200 bg-zinc-900 text-zinc-400 border-zinc-700 hover:border-zinc-500">
                        <span class="text-xl">💞</span>
                        <span class="text-center leading-tight text-xs">Ambos</span>
                    </button>
                </div>
                <input type="hidden" id="settings_sexual_preference" wire:model="sexual_preference" />
                @error('sexual_preference') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- ── BIO ── --}}
            <div class="space-y-1.5">
                <label class="text-sm font-medium text-zinc-300">Sobre ti <span class="text-zinc-500 text-xs font-normal">(mín. 10 caracteres)</span></label>
                <textarea wire:model="bio" rows="3" maxlength="500"
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors resize-none text-sm"
                    placeholder="Cuéntanos algo de ti..."></textarea>
                @error('bio') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- ── SUBMIT ── --}}
            <div class="flex items-center gap-4 pt-1">
                <flux:button variant="primary" type="submit">
                    {{ __('Guardar cambios') }}
                </flux:button>
                <x-action-message on="profile-updated">
                    ✓ {{ __('Guardado') }}
                </x-action-message>
            </div>
        </form>

        <livewire:pages::settings.delete-user-form />
    </x-pages::settings.layout>
</section>

<script>
    const ACTIVE_CLS   = ['bg-vicio-600', 'text-white', 'border-vicio-500'];
    const INACTIVE_CLS = ['bg-zinc-900', 'text-zinc-400', 'border-zinc-700'];

    function settingsSelect(btn) {
        const group = btn.dataset.group;
        document.querySelectorAll(`[data-group="${group}"]`).forEach(b => {
            b.classList.remove(...ACTIVE_CLS);
            b.classList.add(...INACTIVE_CLS);
        });
        btn.classList.remove(...INACTIVE_CLS);
        btn.classList.add(...ACTIVE_CLS);

        // Actualizar el hidden input y notificar a Livewire
        const fieldMap = { gender_identity: 'settings_gender_identity', sexual_preference: 'settings_sexual_preference' };
        const input = document.getElementById(fieldMap[group]);
        if (input) {
            input.value = btn.dataset.value;
            input.dispatchEvent(new Event('input'));
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Marcar los botones según los valores actuales de Livewire
        const gender = document.getElementById('settings_gender_identity')?.value;
        const pref   = document.getElementById('settings_sexual_preference')?.value;

        if (gender) {
            const btn = document.querySelector(`[data-group="gender_identity"][data-value="${gender}"]`);
            if (btn) settingsSelect(btn);
        }
        if (pref) {
            const btn = document.querySelector(`[data-group="sexual_preference"][data-value="${pref}"]`);
            if (btn) settingsSelect(btn);
        }
    });
</script>
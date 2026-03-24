<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="overflow-hidden" style="background-color: #A678C8;">
<div class="w-full max-w-[430px] mx-auto flex flex-col" style="height: 100dvh; background-color: #A678C8;">

    @php
        $inputStyle = 'background-color: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 14px 16px; color: white; font-size: 16px; width: 100%; outline: none;';
        $labelStyle = 'color: rgba(255,255,255,0.85); font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px;';
        $errorStyle = 'color: #fca5a5; font-size: 12px; margin-top: 4px; display: block;';
    @endphp

    {{-- Header --}}
    <div class="shrink-0 flex items-center px-4 py-4 gap-3" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
        <a href="{{ route('admin.users.index') }}" wire:navigate
            class="shrink-0 size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
            style="background-color: rgba(255,255,255,0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        {{-- Avatar + nombre en header --}}
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="size-10 rounded-full overflow-hidden shrink-0" style="outline: 2px solid rgba(255,255,255,0.3); outline-offset: 1px;">
                @if($user->profile_photo_path)
                    <img src="{{ Storage::url($user->profile_photo_path) }}" class="size-full object-cover" alt="{{ $user->name }}">
                @else
                    <div class="size-full flex items-center justify-center font-bold text-white" style="background-color: #2d0a3e;">
                        {{ strtoupper(substr($user->username ?? $user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="min-w-0">
                <h1 class="text-white font-bold text-lg leading-tight truncate">{{ $user->username ?? $user->name }}</h1>
                <p class="text-xs truncate" style="color: rgba(255,255,255,0.55);">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- Contenido scrollable --}}
    <div class="flex-1 overflow-y-auto px-4 py-5 space-y-4">

        {{-- Errores globales --}}
        @if($errors->any())
            <div class="rounded-2xl px-4 py-3 space-y-1" style="background-color: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);">
                @foreach($errors->all() as $error)
                    <p style="color: #fca5a5; font-size: 13px;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Acciones rápidas --}}
        @if($user->id !== auth()->id())
            <div class="rounded-2xl p-4 space-y-3" style="background-color: rgba(255,255,255,0.15);margin-top:20px">
                <p class="text-sm font-semibold" style="color: rgba(255,255,255,0.85);">Acciones rápidas</p>
                <div class="flex gap-2">
                    {{-- Ban/Desban --}}
                    <form method="POST" action="{{ route('admin.users.toggle-ban', $user) }}" class="flex-1">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-semibold transition-opacity hover:opacity-80"
                            style="{{ $user->is_banned
                                ? 'background-color: rgba(74,222,128,0.2); color: #4ade80;'
                                : 'background-color: rgba(239,68,68,0.2); color: #fca5a5;' }}">
                            {{ $user->is_banned ? '✓ Desbanear' : '⊘ Banear' }}
                        </button>
                    </form>
                    {{-- Toggle admin --}}
                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="flex-1">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-semibold transition-opacity hover:opacity-80"
                            style="{{ $user->is_admin
                                ? 'background-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.7);'
                                : 'background-color: rgba(255,255,255,0.25); color: white;' }}">
                            {{ $user->is_admin ? 'Quitar admin' : 'Dar admin' }}
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="rounded-2xl px-4 py-3" style="background-color: rgba(255,255,255,0.1);">
                <p class="text-xs" style="color: rgba(255,255,255,0.5);">No puedes modificar tu propio rol o estado.</p>
            </div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')

            {{-- Foto de perfil --}}
            <div>
                <label style="{{ $labelStyle }}">Foto de perfil</label>
                @if($user->profile_photo_path)
                    <div class="flex items-center gap-3 mb-3" style="margin-bottom: 10px">
                        <img src="{{ Storage::url($user->profile_photo_path) }}"
                            class="size-16 rounded-2xl object-cover"
                            style="outline: 2px solid rgba(255,255,255,0.3); outline-offset: 1px;">
                        <p style="color: rgba(255,255,255,0.5); font-size: 12px;">Sube una nueva para reemplazarla</p>
                    </div>
                @endif
                <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp"
                    style="{{ $inputStyle }} padding: 12px 16px; font-size: 14px; color: rgba(255,255,255,0.7);">
                @error('profile_photo') <span style="{{ $errorStyle }}">{{ $message }}</span> @enderror
            </div>

            {{-- Nombre completo --}}
            <div>
                <label style="{{ $labelStyle }}">Nombre completo *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    placeholder="Nombre completo"
                    style="{{ $inputStyle }}"
                    onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.25)'">
                @error('name') <span style="{{ $errorStyle }}">{{ $message }}</span> @enderror
            </div>

            {{-- Username --}}
            <div>
                <label style="{{ $labelStyle }}">Username *</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.4); font-size: 16px; pointer-events: none;">@</span>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                        placeholder="nombreusuario"
                        style="{{ $inputStyle }} padding-left: 32px;"
                        onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.25)'">
                </div>
                @error('username') <span style="{{ $errorStyle }}">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label style="{{ $labelStyle }}">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    placeholder="email@ejemplo.com"
                    style="{{ $inputStyle }}"
                    onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.25)'">
                @error('email') <span style="{{ $errorStyle }}">{{ $message }}</span> @enderror
            </div>

            {{-- Edad --}}
            <div>
                <label style="{{ $labelStyle }}">Edad</label>
                <input type="number" name="age" value="{{ old('age', $user->age) }}" min="18" max="99"
                    placeholder="18"
                    style="{{ $inputStyle }}"
                    onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.25)'">
                @error('age') <span style="{{ $errorStyle }}">{{ $message }}</span> @enderror
            </div>

            {{-- Identidad de género --}}
            <div>
                <label style="{{ $labelStyle }}">Me identifico como</label>
                <select name="gender_identity"
                    style="{{ $inputStyle }} appearance: none; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='rgba(255,255,255,0.5)'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 16px center; background-size: 16px;"
                    onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.25)'">
                    <option value="" style="background-color: #6b3a8a;">— Seleccionar —</option>
                    @foreach(['man' => 'Hombre', 'woman' => 'Mujer'] as $val => $label)
                        <option value="{{ $val }}" style="background-color: #6b3a8a;" @selected(old('gender_identity', $user->gender_identity) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('gender_identity') <span style="{{ $errorStyle }}">{{ $message }}</span> @enderror
            </div>

            {{-- Preferencia sexual --}}
            <div>
                <label style="{{ $labelStyle }}">Me gustan</label>
                <select name="sexual_preference"
                    style="{{ $inputStyle }} appearance: none; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='rgba(255,255,255,0.5)'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 16px center; background-size: 16px;"
                    onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.25)'">
                    <option value="" style="background-color: #6b3a8a;">— Seleccionar —</option>
                    @foreach(['man' => 'Hombres', 'woman' => 'Mujeres', 'both' => 'Ambos'] as $val => $label)
                        <option value="{{ $val }}" style="background-color: #6b3a8a;" @selected(old('sexual_preference', $user->sexual_preference) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('sexual_preference') <span style="{{ $errorStyle }}">{{ $message }}</span> @enderror
            </div>

            {{-- Bio --}}
            <div>
                <label style="{{ $labelStyle }}">Bio</label>
                <textarea name="bio" rows="3" maxlength="500" placeholder="Cuéntanos algo..."
                    style="{{ $inputStyle }} resize: none;"
                    onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.25)'">{{ old('bio', $user->bio) }}</textarea>
                @error('bio') <span style="{{ $errorStyle }}">{{ $message }}</span> @enderror
            </div>

            {{-- Botones guardar --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}" wire:navigate
                    class="flex-1 font-semibold py-4 rounded-2xl text-center transition-opacity hover:opacity-80 text-sm"
                    style="background-color: rgba(255,255,255,0.2); color: white;">
                    Cancelar
                </a>
                <button type="submit"
                    class="flex-1 font-bold py-4 rounded-2xl transition-opacity hover:opacity-90 text-sm text-white"
                    style="background-color: #2d0a3e;">
                    Guardar cambios
                </button>
            </div>
        </form>

        {{-- Zona de peligro --}}
        @if($user->id !== auth()->id())
            <div class="rounded-2xl p-4 space-y-3 pb-6" style="background-color: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);margin-bottom:10px">
                <p class="text-sm font-semibold" style="color: #fca5a5;">Zona de peligro</p>
                <p class="text-xs" style="color: rgba(255,255,255,0.5);">Eliminar este usuario borrará su cuenta permanentemente. Esta acción no se puede deshacer.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                    onsubmit="return confirm('¿Seguro que quieres eliminar a {{ addslashes($user->name) }}? Esta acción es irreversible.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full py-3 rounded-xl text-sm font-semibold transition-opacity hover:opacity-80"
                        style="background-color: rgba(239,68,68,0.25); color: #fca5a5;">
                        🗑 Eliminar usuario permanentemente
                    </button>
                </form>
            </div>
        @endif

    </div>

</div>
@fluxScripts
</body>
</html>
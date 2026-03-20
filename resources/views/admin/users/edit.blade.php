<x-layouts::app.sidebar>
    <div class="max-w-2xl mx-auto px-4 py-8 space-y-6">

        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
                class="size-9 rounded-full bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition-colors flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex items-center gap-3 min-w-0">
                {{-- Avatar --}}
                <div class="size-10 rounded-full bg-zinc-800 overflow-hidden flex-shrink-0">
                    @if($user->profile_photo_path)
                        <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="size-full object-cover">
                    @else
                        <div class="size-full flex items-center justify-center text-zinc-400 font-semibold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <h1 class="text-white text-xl font-bold truncate">{{ $user->name }}</h1>
                    <p class="text-zinc-500 text-sm">&#64;{{ $user->username ?? 'sin usuario' }}</p>
                </div>
            </div>
        </div>

        {{-- Errores globales --}}
        @if($errors->any())
            <div class="bg-red-900/30 border border-red-700/50 rounded-xl px-4 py-3 space-y-1">
                @foreach($errors->all() as $error)
                    <p class="text-red-400 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Acciones rápidas --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 space-y-3">
            <p class="text-zinc-400 text-sm font-medium">Acciones rápidas</p>
            <div class="flex flex-wrap gap-2">

                {{-- Toggle ban --}}
                @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.toggle-ban', $user) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $user->is_banned ? 'bg-green-900/40 text-green-400 border border-green-700/50 hover:bg-green-900/60' : 'bg-red-900/40 text-red-400 border border-red-700/50 hover:bg-red-900/60' }}">
                            {{ $user->is_banned ? '✓ Desbanear usuario' : '⊘ Banear usuario' }}
                        </button>
                    </form>

                    {{-- Toggle admin --}}
                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $user->is_admin ? 'bg-zinc-800 text-zinc-400 border border-zinc-700 hover:bg-zinc-700' : 'bg-vicio-400/10 text-vicio-400 border border-vicio-400/30 hover:bg-vicio-400/20' }}">
                            {{ $user->is_admin ? 'Quitar rol admin' : 'Dar rol admin' }}
                        </button>
                    </form>
                @else
                    <p class="text-zinc-600 text-sm italic">No puedes modificar tu propio rol o estado.</p>
                @endif
            </div>
        </div>

        {{-- Formulario principal --}}
        <form
            method="POST"
            action="{{ route('admin.users.update', $user) }}"
            enctype="multipart/form-data"
            class="space-y-5"
        >
            @csrf
            @method('PUT')

            {{-- Foto de perfil --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-zinc-300">Foto de perfil</label>
                @if($user->profile_photo_path)
                    <div class="flex items-center gap-3">
                        <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Foto actual"
                            class="size-16 rounded-xl object-cover border border-zinc-700">
                        <p class="text-zinc-500 text-xs">Sube una nueva imagen para reemplazarla</p>
                    </div>
                @endif
                <input
                    type="file"
                    name="profile_photo"
                    accept="image/jpeg,image/png,image/webp"
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-2.5 text-zinc-400 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-zinc-700 file:text-zinc-300 file:text-sm hover:file:bg-zinc-600 transition-colors"
                >
                @error('profile_photo')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nombre completo --}}
            <div class="space-y-1.5">
                <label class="text-sm font-medium text-zinc-300">Nombre completo *</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm"
                >
                @error('name')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- Username --}}
            <div class="space-y-1.5">
                <label class="text-sm font-medium text-zinc-300">Username *</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500 text-sm select-none">@</span>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username', $user->username) }}"
                        required
                        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl pl-8 pr-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm"
                    >
                </div>
                @error('username')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="space-y-1.5">
                <label class="text-sm font-medium text-zinc-300">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm"
                >
                @error('email')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- Edad --}}
            <div class="space-y-1.5">
                <label class="text-sm font-medium text-zinc-300">Edad</label>
                <input
                    type="number"
                    name="age"
                    value="{{ old('age', $user->age) }}"
                    min="18"
                    max="99"
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm"
                >
                @error('age')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- Identidad de género --}}
<div class="space-y-1.5">
    <label class="text-sm font-medium text-zinc-300">Identidad de género</label>
    <select name="gender_identity"
        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm">
        <option value="">— Sin especificar —</option>
        @foreach(['man' => 'Hombre', 'woman' => 'Mujer'] as $val => $label)
            <option value="{{ $val }}" @selected(old('gender_identity', $user->gender_identity) === $val)>{{ $label }}</option>
        @endforeach
    </select>
</div>

            {{-- Preferencia sexual --}}
<div class="space-y-1.5">
    <label class="text-sm font-medium text-zinc-300">Preferencia sexual</label>
    <select name="sexual_preference"
        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm">
        <option value="">— Sin especificar —</option>
        @foreach(['man' => 'Hombre', 'woman' => 'Mujeres', 'bi' => 'Ambos'] as $val => $label)
            <option value="{{ $val }}" @selected(old('sexual_preference', $user->sexual_preference) === $val)>{{ $label }}</option>
        @endforeach
    </select>
</div>

            {{-- Bio --}}
            <div class="space-y-1.5">
                <label class="text-sm font-medium text-zinc-300">Bio</label>
                <textarea
                    name="bio"
                    rows="3"
                    maxlength="500"
                    class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors resize-none text-sm"
                >{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="flex gap-3 pt-2">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="flex-1 bg-zinc-800 text-zinc-300 font-semibold py-3 rounded-xl text-center hover:bg-zinc-700 transition-colors text-sm"
                >
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="flex-1 vicio-gradient text-white font-semibold py-3 rounded-xl hover:opacity-90 transition-opacity text-sm"
                >
                    Guardar cambios
                </button>
            </div>
        </form>

        {{-- Zona de peligro --}}
        @if($user->id !== auth()->id())
            <div class="bg-red-950/20 border border-red-900/40 rounded-2xl p-4 space-y-3">
                <p class="text-red-400 text-sm font-medium">Zona de peligro</p>
                <p class="text-zinc-500 text-xs">Eliminar este usuario borrará su cuenta permanentemente. Esta acción no se puede deshacer.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                    onsubmit="return confirm('¿Seguro que quieres eliminar a {{ addslashes($user->name) }}? Esta acción es irreversible.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-900/40 border border-red-700/50 text-red-400 rounded-xl text-sm font-medium hover:bg-red-900/60 transition-colors">
                        Eliminar usuario
                    </button>
                </form>
            </div>
        @endif

    </div>
</x-layouts::app.sidebar>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="overflow-hidden" style="background-color: #A678C8;">
<div class="w-full max-w-[430px] mx-auto flex flex-col" style="height: 100dvh; background-color: #A678C8;">

    {{-- Header --}}
    <div class="shrink-0 flex items-center px-4 py-4 gap-3" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
        <a href="{{ route('dashboard') }}" wire:navigate
            class="shrink-0 size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
            style="background-color: rgba(255,255,255,0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-white font-bold text-xl flex-1 leading-tight">Usuarios</h1>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mx-4 mt-4 rounded-2xl px-4 py-3 text-sm font-medium text-white" style="background-color: rgba(74,222,128,0.2); border: 1px solid rgba(74,222,128,0.3);">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mx-4 mt-4 rounded-2xl px-4 py-3 text-sm font-medium" style="background-color: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Búsqueda + filtros --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="shrink-0" style="padding: 16px 16px 0 16px;">
        {{-- Buscador --}}
        <div class="relative" style="margin-bottom: 10px;">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: rgba(255,255,255,0.5);">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Buscar usuario..."
                class="w-full rounded-2xl focus:outline-none transition-colors"
                style="background-color: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.2); font-size: 16px; color: white; padding: 12px 16px 12px 42px;">
        </div>

        {{-- Filtros --}}
        <div class="flex gap-2">
            @foreach(['all' => 'Todos', 'admins' => 'Admins', 'banned' => 'Baneados'] as $val => $label)
                @php $active = request('filter', '') === ($val === 'all' ? '' : $val); @endphp
                <button type="submit" name="filter" value="{{ $val === 'all' ? '' : $val }}"
                    class="flex-1 py-2 rounded-xl text-xs font-semibold transition-opacity hover:opacity-80"
                    style="{{ $active
                        ? 'background-color: #2d0a3e; color: white;'
                        : 'background-color: rgba(255,255,255,0.18); color: rgba(255,255,255,0.75);' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </form>

    {{-- Banner filtro por fiesta --}}
    @isset($party)
        <div class="flex items-center justify-between rounded-2xl px-4 py-2.5"
            style="margin: 10px 16px 0 16px; background-color: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
            <span class="text-white text-xs font-medium">🎉 {{ $party->name }}</span>
            <a href="{{ route('admin.users.index') }}" wire:navigate class="text-xs" style="color: rgba(255,255,255,0.5);">✕ Quitar</a>
        </div>
    @endisset

    {{-- Lista --}}
    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-2">

        @if($users->isEmpty())
            <div class="flex flex-col items-center justify-center py-32 text-center gap-4">
                <div class="text-5xl">👤</div>
                <p class="font-semibold text-white text-base">No se encontraron usuarios</p>
            </div>
        @else
            @foreach($users as $user)
                <div class="rounded-2xl px-4 py-3 flex items-center gap-3"
                    style="background-color: rgba(255,255,255,0.15);">

                    {{-- Avatar --}}
                    <div class="size-11 rounded-full overflow-hidden shrink-0" style="outline: 2px solid rgba(255,255,255,0.3); outline-offset: 1px;">
                        @if($user->profile_photo_path)
                            <img src="{{ Storage::url($user->profile_photo_path) }}" class="size-full object-cover"/>
                        @else
                            <div class="size-full flex items-center justify-center font-bold text-sm text-white" style="background-color: #2d0a3e;">
                                {{ strtoupper(substr($user->username ?? $user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <p class="text-white font-semibold text-sm truncate">{{ $user->username ?? $user->name }}</p>
                            @if($user->is_admin)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold" style="background-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.9);">Admin</span>
                            @endif
                            @if($user->is_banned)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold" style="background-color: rgba(239,68,68,0.2); color: #fca5a5;">Baneado</span>
                            @endif
                        </div>
                        @if($user->currentParty)
                            <p class="text-xs truncate mt-0.5" style="color: rgba(255,255,255,0.6);">🎉 {{ $user->currentParty->name }}</p>
                        @else
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.35);">Sin fiesta activa</p>
                        @endif
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center gap-1.5 shrink-0">
                        {{-- Ban/Desban --}}
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-ban', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    title="{{ $user->is_banned ? 'Desbanear' : 'Banear' }}"
                                    class="size-8 rounded-xl flex items-center justify-center transition-opacity hover:opacity-80"
                                    style="{{ $user->is_banned
                                        ? 'background-color: rgba(239,68,68,0.25);'
                                        : 'background-color: rgba(255,255,255,0.18);' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        style="color: {{ $user->is_banned ? '#fca5a5' : 'rgba(255,255,255,0.7)' }};">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </form>
                        @endif

                        {{-- Editar --}}
                        <a href="{{ route('admin.users.edit', $user) }}" wire:navigate
                            title="Editar"
                            class="size-8 rounded-xl flex items-center justify-center transition-opacity hover:opacity-80"
                            style="background-color: rgba(255,255,255,0.18);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: rgba(255,255,255,0.7);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>

                        {{-- Eliminar --}}
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                onsubmit="return confirm('¿Eliminar a {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    title="Eliminar"
                                    class="size-8 rounded-xl flex items-center justify-center transition-opacity hover:opacity-80"
                                    style="background-color: rgba(239,68,68,0.2);">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #fca5a5;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            @endforeach

            {{-- Paginación --}}
            @if($users->hasPages())
                <div class="flex items-center justify-between pt-2 pb-4 gap-2">
                    @if($users->onFirstPage())
                        <span class="px-4 py-2 rounded-xl text-sm font-medium" style="background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.3);">← Anterior</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" wire:navigate
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-opacity hover:opacity-80 text-white"
                            style="background-color: rgba(255,255,255,0.2);">← Anterior</a>
                    @endif

                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">
                        {{ $users->currentPage() }} / {{ $users->lastPage() }}
                    </span>

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" wire:navigate
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-opacity hover:opacity-80 text-white"
                            style="background-color: rgba(255,255,255,0.2);">Siguiente →</a>
                    @else
                        <span class="px-4 py-2 rounded-xl text-sm font-medium" style="background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.3);">Siguiente →</span>
                    @endif
                </div>
            @endif
        @endif

    </div>

</div>
@fluxScripts
</body>
</html>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-dvh" style="background-color: #0a0212;">
<div class="w-full max-w-[430px] mx-auto flex flex-col" style="min-height: 100dvh;">

    {{-- Header --}}
    <div class="shrink-0 flex items-center justify-between px-4 py-3 border-b border-zinc-800">
        <a href="{{ route('dashboard') }}" wire:navigate
            class="size-9 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center hover:bg-zinc-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="text-white font-bold text-base">Usuarios</span>
        <div class="size-9"></div>{{-- spacer --}}
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mx-4 mt-4 bg-green-900/30 border border-green-700/50 text-green-400 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mx-4 mt-4 bg-red-900/30 border border-red-700/50 text-red-400 rounded-xl px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Búsqueda + filtros --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="px-4 pt-4 space-y-3">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Buscar usuario..."
                class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl pl-10 pr-4 py-3 text-white text-sm placeholder-zinc-600 focus:outline-none focus:border-vicio-400 transition-colors"
                style="font-size: 16px;">
        </div>
        <div class="flex gap-2">
            @foreach(['all' => 'Todos', 'admins' => 'Admins', 'banned' => 'Baneados'] as $val => $label)
                <button type="submit" name="filter" value="{{ $val === 'all' ? '' : $val }}"
                    @class([
                        'flex-1 py-2 rounded-xl text-xs font-semibold transition-colors',
                        'vicio-gradient text-white' => request('filter', '') === ($val === 'all' ? '' : $val),
                        'bg-zinc-900 border border-zinc-800 text-zinc-400' => request('filter', '') !== ($val === 'all' ? '' : $val),
                    ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </form>

    {{-- Banner filtro por fiesta --}}
    @isset($party)
        <div class="mx-4 mt-3 flex items-center justify-between bg-vicio-900/30 border border-vicio-700/50 rounded-xl px-4 py-2.5">
            <span class="text-vicio-300 text-xs font-medium">🎉 {{ $party->name }}</span>
            <a href="{{ route('admin.users.index') }}" wire:navigate class="text-zinc-500 hover:text-zinc-300 text-xs transition-colors">✕</a>
        </div>
    @endisset

    {{-- Lista --}}
    <div class="flex-1 px-4 py-4 space-y-2">

        @if($users->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-center gap-4">
                <div class="size-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-7 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-zinc-400 font-medium">No se encontraron usuarios</p>
            </div>
        @else
            @foreach($users as $user)
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-3 flex items-center gap-3">
                    {{-- Avatar --}}
                    <div class="size-11 rounded-full overflow-hidden shrink-0 bg-zinc-800">
                        @if($user->profile_photo_path)
                            <img src="{{ Storage::url($user->profile_photo_path) }}" class="size-full object-cover"/>
                        @else
                            <div class="size-full flex items-center justify-center text-zinc-400 font-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <p class="text-white font-semibold text-sm truncate">{{ $user->username ?? $user->name }}</p>
                            @if($user->is_admin)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-vicio-400/20 text-vicio-400">Admin</span>
                            @endif
                            @if($user->is_banned)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-500/20 text-red-400">Baneado</span>
                            @endif
                        </div>
                        @if($user->currentParty)
                            <p class="text-vicio-400 text-xs truncate mt-0.5">🎉 {{ $user->currentParty->name }}</p>
                        @else
                            <p class="text-zinc-600 text-xs mt-0.5">Sin fiesta activa</p>
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
                                    class="size-8 rounded-xl {{ $user->is_banned ? 'bg-red-900/40 hover:bg-red-900/60' : 'bg-zinc-800 hover:bg-zinc-700' }} flex items-center justify-center transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 {{ $user->is_banned ? 'text-red-400' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </form>
                        @endif

                        {{-- Editar --}}
                        <a href="{{ route('admin.users.edit', $user) }}" wire:navigate
                            title="Editar"
                            class="size-8 rounded-xl bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                    class="size-8 rounded-xl bg-zinc-800 hover:bg-red-900/50 flex items-center justify-center transition-colors group">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 group-hover:text-red-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                <div class="pt-2 pb-4">
                    {{ $users->links() }}
                </div>
            @endif

            <p class="text-zinc-700 text-xs text-center pb-4">{{ $users->total() }} usuario{{ $users->total() !== 1 ? 's' : '' }}</p>
        @endif
    </div>

</div>
@fluxScripts
</body>
</html>
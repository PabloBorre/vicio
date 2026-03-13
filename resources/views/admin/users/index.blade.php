<x-layouts::app.sidebar>
    <div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-white text-2xl font-bold">Usuarios</h1>
                <p class="text-zinc-500 text-sm mt-0.5">Gestiona todos los usuarios de VicioApp</p>
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="bg-green-900/30 border border-green-700/50 text-green-400 rounded-xl px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-900/30 border border-red-700/50 text-red-400 rounded-xl px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Búsqueda y filtros --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Buscar por nombre, usuario o email..."
                    class="w-full bg-zinc-900 border border-zinc-800 rounded-xl pl-10 pr-4 py-2.5 text-white text-sm placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors"
                >
            </div>
            <div class="flex gap-2">
                @foreach(['all' => 'Todos', 'admins' => 'Admins', 'banned' => 'Baneados'] as $val => $label)
                    <button
                        type="submit"
                        name="filter"
                        value="{{ $val === 'all' ? '' : $val }}"
                        @class([
                            'px-4 py-2.5 rounded-xl text-sm font-medium transition-colors',
                            'vicio-gradient text-white' => request('filter', '') === ($val === 'all' ? '' : $val),
                            'bg-zinc-900 border border-zinc-800 text-zinc-400 hover:bg-zinc-800' => request('filter', '') !== ($val === 'all' ? '' : $val),
                        ])
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </form>

        {{-- Tabla --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
            @if($users->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center gap-3">
                    <div class="size-12 rounded-full bg-zinc-800 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <p class="text-zinc-400 font-medium">No se encontraron usuarios</p>
                    <p class="text-zinc-600 text-sm">Prueba con otro filtro o búsqueda</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-800">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden md:table-cell">Email</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider hidden lg:table-cell">Fiesta actual</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Rol / Estado</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @foreach($users as $user)
                                <tr class="hover:bg-zinc-800/40 transition-colors">

                                    {{-- Avatar + nombre --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-9 rounded-full bg-zinc-800 overflow-hidden flex-shrink-0">
                                                @if($user->profile_photo_path)
                                                    <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="size-full object-cover">
                                                @else
                                                    <div class="size-full flex items-center justify-center text-zinc-500 text-sm font-semibold">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-white font-medium leading-tight">{{ $user->name }}</p>
                                                <p class="text-zinc-500 text-xs">&#64;{{ $user->username ?? '—' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Email --}}
                                    <td class="px-5 py-4 text-zinc-400 hidden md:table-cell">
                                        {{ $user->email ?? '—' }}
                                    </td>

                                    {{-- Fiesta actual --}}
                                    <td class="px-5 py-4 hidden lg:table-cell">
                                        @if($user->currentParty)
                                            <span class="text-vicio-400 text-xs font-medium">{{ $user->currentParty->name }}</span>
                                        @else
                                            <span class="text-zinc-600 text-xs">Sin fiesta</span>
                                        @endif
                                    </td>

                                    {{-- Badges --}}
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            @if($user->is_admin)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-vicio-400/20 text-vicio-400 border border-vicio-400/30">Admin</span>
                                            @endif
                                            @if($user->is_banned)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">Baneado</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Activo</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2 justify-end">

                                            {{-- Toggle ban --}}
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.toggle-ban', $user) }}">
                                                    @csrf @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        title="{{ $user->is_banned ? 'Desbanear' : 'Banear' }}"
                                                        class="size-8 rounded-lg {{ $user->is_banned ? 'bg-red-900/40 hover:bg-red-900/60' : 'bg-zinc-800 hover:bg-zinc-700' }} flex items-center justify-center transition-colors"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 {{ $user->is_banned ? 'text-red-400' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Editar --}}
                                            <a
                                                href="{{ route('admin.users.edit', $user) }}"
                                                wire:navigate
                                                title="Editar"
                                                class="size-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition-colors"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            {{-- Eliminar --}}
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                    onsubmit="return confirm('¿Seguro que quieres eliminar a {{ addslashes($user->name) }}? Esta acción no se puede deshacer.')">
                                                    @csrf @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        title="Eliminar"
                                                        class="size-8 rounded-lg bg-zinc-800 hover:bg-red-900/50 flex items-center justify-center transition-colors group"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 group-hover:text-red-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($users->hasPages())
                    <div class="px-5 py-4 border-t border-zinc-800">
                        {{ $users->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Contador --}}
        <p class="text-zinc-600 text-xs text-right">
            {{ $users->total() }} usuario{{ $users->total() !== 1 ? 's' : '' }} en total
        </p>

    </div>
</x-layouts::app.sidebar>
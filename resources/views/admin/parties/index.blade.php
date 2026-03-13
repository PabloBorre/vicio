<x-layouts::app.sidebar>
    <div class="max-w-5xl mx-auto px-4 py-8 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-white text-2xl font-bold">Fiestas</h1>
                <p class="text-zinc-500 text-sm mt-0.5">Gestiona todas las fiestas de VicioApp</p>
            </div>
            <a
                href="{{ route('admin.parties.create') }}"
                wire:navigate
                class="vicio-gradient text-white font-semibold px-4 py-2.5 rounded-xl flex items-center gap-2 hover:opacity-90 transition-opacity text-sm"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva fiesta
            </a>
        </div>

        {{-- Flash message --}}
        @if(session('success'))
            <div class="bg-green-900/30 border border-green-700/50 text-green-400 rounded-xl px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabla --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
            @if($parties->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center gap-3">
                    <div class="size-12 rounded-full bg-zinc-800 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-zinc-400 font-medium">No hay fiestas todavía</p>
                    <p class="text-zinc-600 text-sm">Crea la primera para empezar</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-800">
                            <th class="text-left text-zinc-500 font-medium px-5 py-3">Fiesta</th>
                            <th class="text-left text-zinc-500 font-medium px-5 py-3 hidden md:table-cell">Inicio</th>
                            <th class="text-left text-zinc-500 font-medium px-5 py-3 hidden sm:table-cell">Asistentes</th>
                            <th class="text-left text-zinc-500 font-medium px-5 py-3">Estado</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        @foreach($parties as $party)
                            <tr class="hover:bg-zinc-800/50 transition-colors">
                                {{-- Nombre + ubicación --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($party->cover_image)
                                            <img src="{{ asset('storage/' . $party->cover_image) }}" class="size-9 rounded-lg object-cover shrink-0" />
                                        @else
                                            <div class="size-9 rounded-lg vicio-gradient flex items-center justify-center shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-white" viewBox="0 0 24 24">
                                                    <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-white font-medium truncate">{{ $party->name }}</p>
                                            @if($party->location)
                                                <p class="text-zinc-500 text-xs truncate">{{ $party->location }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Fecha --}}
                                <td class="px-5 py-4 text-zinc-400 hidden md:table-cell whitespace-nowrap">
                                    {{ $party->starts_at->format('d/m/Y') }}<br>
                                    <span class="text-zinc-600 text-xs">{{ $party->starts_at->format('H:i') }}</span>
                                </td>

                                {{-- Asistentes --}}
                                <td class="px-5 py-4 text-zinc-400 hidden sm:table-cell">
                                    {{ $party->users_count }}
                                </td>

                                {{-- Estado + cambio rápido --}}
                                <td class="px-5 py-4">
                                    @php
                                        $statusConfig = [
                                            'draft'        => ['label' => 'Borrador',        'class' => 'bg-zinc-800 text-zinc-400'],
                                            'registration' => ['label' => 'Registro',         'class' => 'bg-blue-900/50 text-blue-400'],
                                            'countdown'    => ['label' => 'Cuenta atrás',     'class' => 'bg-yellow-900/50 text-yellow-400'],
                                            'active'       => ['label' => 'Activa',           'class' => 'bg-green-900/50 text-green-400'],
                                            'finished'     => ['label' => 'Finalizada',       'class' => 'bg-zinc-800 text-zinc-500'],
                                        ];
                                        $cfg = $statusConfig[$party->status] ?? $statusConfig['draft'];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $cfg['class'] }}">
                                        {{ $cfg['label'] }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2 justify-end">
                                        {{-- Ver QR --}}
                                        <a
                                            href="{{ route('admin.parties.qr', $party) }}"
                                            target="_blank"
                                            title="Ver enlace QR"
                                            class="size-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition-colors"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                            </svg>
                                        </a>

                                        {{-- Usuarios de la fiesta --}}
                                        <a href="{{ route('admin.users.index', ['party' => $party->id]) }}" wire:navigate title="Ver usuarios"
                                            class="size-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition-colors">
    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
</a>

                                        {{-- Editar --}}
                                        <a
                                            href="{{ route('admin.parties.edit', $party) }}"
                                            wire:navigate
                                            title="Editar"
                                            class="size-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition-colors"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- Eliminar --}}
                                        <form method="POST" action="{{ route('admin.parties.destroy', $party) }}"
                                            onsubmit="return confirm('¿Seguro que quieres eliminar esta fiesta?')">
                                            @csrf @method('DELETE')
                                            <button
                                                type="submit"
                                                title="Eliminar"
                                                class="size-8 rounded-lg bg-zinc-800 hover:bg-red-900/50 flex items-center justify-center transition-colors"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-400 hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-layouts::app.sidebar>
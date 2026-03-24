<?php
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Inicio')] class extends Component
{
    public function render()
    {
        return view('dashboard');
    }
}; ?>

<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="h-dvh overflow-hidden" style="background-color: #A678C8;">
<div class="w-full max-w-[430px] mx-auto flex flex-col overflow-y-auto" style="height: 100dvh; background-color: #A678C8; ">

    {{-- Header --}}
    <div class="shrink-0 flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
        <div class="flex items-center gap-2">
            <span class="text-white font-bold text-xl tracking-tight">VicioApp</span>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="flex-1 flex flex-col items-center px-4 py-8 gap-6" style="margin-top: 10vh;">

        @if(auth()->user()->is_admin)

            {{-- ══════════════════════════════════
                 VISTA ADMIN
            ══════════════════════════════════ --}}

            {{-- Avatar + saludo --}}
            <div class="flex flex-col items-center gap-3 w-full pt-4">
                <div class="size-20 rounded-full flex items-center justify-center shadow-lg" style="background-color: #49197C; ring: 4px solid rgba(255,255,255,0.3);">
                    <img src="{{ asset('images/Logo.png') }}" alt="VicioApp" width="70" height="70">
                </div>
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-white">
                        Hola, {{ auth()->user()->username ?? auth()->user()->name }} 
                    </h1>
                    <p class="text-sm mt-1" style="color: rgba(255,255,255,0.65);">Panel de administración</p>
                </div>
            </div>

            {{-- Sección Fiestas --}}
            <div class="flex flex-col gap-3 w-full">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.55);">Fiestas</p>

                {{-- Botón principal: oscuro --}}
                <a href="/admin/parties" wire:navigate
                    class="w-full text-white font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 transition-opacity hover:opacity-90"
                    style="background-color: #2d0a3e;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Ver todas las fiestas
                </a>

                {{-- Botón secundario: blanco semitransparente --}}
                <a href="/admin/parties/create" wire:navigate
                    class="w-full font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 transition-opacity hover:opacity-90"
                    style="background-color: rgba(255,255,255,0.25); color: #1a0a2e;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear nueva fiesta
                </a>
            </div>

            {{-- Sección Usuarios --}}
            <div class="flex flex-col gap-3 w-full">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.55);">Usuarios</p>

                <a href="/admin/users" wire:navigate
                    class="w-full font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 transition-opacity hover:opacity-90"
                    style="background-color: rgba(255,255,255,0.25); color: #1a0a2e;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Gestionar usuarios
                </a>
            </div>

        @else

            {{-- ══════════════════════════════════
                 VISTA USUARIO NORMAL
            ══════════════════════════════════ --}}
            @php
                $userId = auth()->id();
                $matches = \App\Models\PartyMatch::where('user1_id', $userId)->orWhere('user2_id', $userId)->count();
                $unread  = \App\Models\Message::whereHas('match', fn($q) => $q->where('user1_id', $userId)->orWhere('user2_id', $userId))
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();
                $likes   = \App\Models\Swipe::where('swiped_id', $userId)->where('direction', 'like')->count();
                $currentParty = auth()->user()->parties()
                    ->whereIn('parties.status', ['registration', 'countdown', 'active'])
                    ->latest('pivot_joined_at')
                    ->first();
            @endphp

            {{-- Avatar + saludo --}}
            <div class="flex flex-col items-center gap-3 w-full pt-4">
                <div class="size-20 rounded-full overflow-hidden shadow-lg" style="outline: 3px solid rgba(255,255,255,0.4); outline-offset: 2px;">
                    <img
                        src="{{ auth()->user()->profile_photo_url }}"
                        alt="{{ auth()->user()->username }}"
                        class="w-full h-full object-cover"
                    >
                </div>
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-white">
                        Hola, {{ auth()->user()->username ?? auth()->user()->name }}
                    </h1>
                    <p class="text-sm mt-1" style="color: rgba(255,255,255,0.65);">¿Listo para la noche?</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-3 w-full">
                <div class="rounded-2xl p-4 flex flex-col items-center gap-1" style="background-color: rgba(255,255,255,0.2);">
                    <span class="text-2xl font-bold text-white">{{ $matches }}</span>
                    <span class="text-xs font-medium" style="color: rgba(255,255,255,0.65);">Matches</span>
                </div>
                <div class="rounded-2xl p-4 flex flex-col items-center gap-1" style="background-color: rgba(255,255,255,0.2);">
                    <span class="text-2xl font-bold text-white">{{ $unread }}</span>
                    <span class="text-xs font-medium" style="color: rgba(255,255,255,0.65);">Sin leer</span>
                </div>
                <div class="rounded-2xl p-4 flex flex-col items-center gap-1" style="background-color: rgba(255,255,255,0.2);">
                    <span class="text-2xl font-bold text-white">{{ $likes }}</span>
                    <span class="text-xs font-medium" style="color: rgba(255,255,255,0.65);">Likes</span>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex flex-col gap-3 w-full">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.55);">Mis acciones</p>

                {{-- Mis chats --}}
                <a href="{{ route('chats.index') }}" wire:navigate
                    class="w-full font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 transition-opacity hover:opacity-90"
                    style="background-color: rgba(255,255,255,0.25); color: #1a0a2e;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Mis chats
                    @if($unread > 0)
                        <span class="ml-auto size-5 rounded-full text-white text-xs font-bold flex items-center justify-center" style="background-color: #49197C;">{{ $unread }}</span>
                    @endif
                </a>

                {{-- Editar perfil --}}
                <a href="{{ route('profile.edit') }}" 
                    class="w-full font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 transition-opacity hover:opacity-90"
                    style="background-color: rgba(255,255,255,0.25); color: #1a0a2e;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Editar perfil
                </a>

                {{-- Volver a la fiesta --}}
                @if($currentParty)
                    @php
                        $partyRoute = $currentParty->status === 'active'
                            ? route('party.swipe', $currentParty->qr_code)
                            : route('party.waiting', $currentParty->qr_code);
                    @endphp
                    <a href="{{ $partyRoute }}" wire:navigate
                        class="w-full text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-3 transition-opacity hover:opacity-90"
                        style="background-color: #2d0a3e;">
                        <span>{{ $currentParty->status === 'active' ? '🔥 Volver a la fiesta' : '⏳ Sala de espera' }}</span>
                    </a>
                @endif
            </div>

        @endif

    </div>
</div>

@auth
    <livewire:chat.chat-notification />
    @if(!auth()->user()->is_admin)
        <livewire:auth.ban-watcher />
    @endif
    @include('partials.push-prompt')
@endauth

@fluxScripts
</body>
</html>
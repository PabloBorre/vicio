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
<body class="h-dvh bg-zinc-950 overflow-hidden">
<div class="w-full max-w-[430px] mx-auto flex flex-col overflow-y-auto" style="height: 100dvh; background-color: #0a0212;">

    {{-- Header --}}
    <div class="shrink-0 flex items-center justify-between px-4 py-3 border-b border-zinc-800">
        <div class="flex items-center gap-2">
            <div class="size-8 rounded-full vicio-gradient flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4 fill-white">
                    <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
                </svg>
            </div>
            <span class="text-white font-bold text-lg">VicioApp</span>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="flex-1 flex flex-col items-center justify-center px-4 py-8 gap-8 text-center">

        {{-- Bienvenida --}}
        <div class="flex flex-col items-center gap-3" style="margin-top: 100px">
            <div class="size-16 rounded-full vicio-gradient flex items-center justify-center shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-8 fill-white">
                    <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Hola, {{ auth()->user()->username ?? auth()->user()->name }}
                </h1>
                <p class="text-zinc-500 text-sm mt-1">¿Listo para la noche?</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-3 w-full max-w-sm">
            @php
                $userId = auth()->id();
                $matches = \App\Models\PartyMatch::where('user1_id', $userId)->orWhere('user2_id', $userId)->count();
                $unread  = \App\Models\Message::whereHas('match', fn($q) => $q->where('user1_id', $userId)->orWhere('user2_id', $userId))
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();
                $likes   = \App\Models\Swipe::where('swiped_id', $userId)->where('direction', 'like')->count();
            @endphp

            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 flex flex-col items-center gap-1">
                <span class="text-2xl font-bold text-vicio-300">{{ $matches }}</span>
                <span class="text-zinc-500 text-xs">Matches</span>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 flex flex-col items-center gap-1">
                <span class="text-2xl font-bold text-vicio-300">{{ $unread }}</span>
                <span class="text-zinc-500 text-xs">Sin leer</span>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 flex flex-col items-center gap-1">
                <span class="text-2xl font-bold text-vicio-300">{{ $likes }}</span>
                <span class="text-zinc-500 text-xs">Likes</span>
            </div>
        </div>

        {{-- Acciones principales --}}
        <div class="flex flex-col gap-3 w-full max-w-sm">
            <a href="{{ route('chats.index') }}" wire:navigate
                class="w-full bg-zinc-900 border border-zinc-800 text-white font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 hover:border-zinc-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-vicio-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Mis chats
                @if($unread > 0)
                    <span class="ml-auto size-5 rounded-full bg-vicio text-white text-xs font-bold flex items-center justify-center">{{ $unread }}</span>
                @endif
            </a>

            {{-- Fiesta activa --}}
            @if(!auth()->user()->is_admin)
                @php
                    $currentParty = auth()->user()->parties()
                        ->whereIn('parties.status', ['registration', 'countdown', 'active'])
                        ->latest('pivot_joined_at')
                        ->first();
                @endphp
                @if($currentParty)
                    @php
                        $partyRoute = $currentParty->status === 'active'
                            ? route('party.swipe', $currentParty->qr_code)
                            : route('party.waiting', $currentParty->qr_code);
                    @endphp
                    <a href="{{ $partyRoute }}" wire:navigate
                       class="w-full vicio-gradient text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-3 hover:opacity-90 transition-opacity">
                        <span>{{ $currentParty->status === 'active' ? '🔥 Volver a la fiesta' : '⏳ Sala de espera' }}</span>
                    </a>
                @endif
            @endif

            {{-- Editar perfil --}}
            <a href="{{ route('profile.edit') }}" wire:navigate
               class="w-full bg-zinc-900 border border-zinc-800 text-white font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 hover:border-zinc-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-vicio-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Editar perfil
            </a>
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
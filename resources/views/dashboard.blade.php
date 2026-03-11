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

<x-layouts::app.sidebar>
    <div class="h-full flex flex-col items-center justify-center px-4 py-8 gap-8 text-center">

        {{-- Bienvenida --}}
        <div class="flex flex-col items-center gap-3">
            <div class="size-16 rounded-full vicio-gradient flex items-center justify-center shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-8 fill-white">
                    <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Hola, {{ auth()->user()->username ?? auth()->user()->name }} 👋
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

            {{-- Escanear QR --}}
            <a
                href="{{ '#' }}"
                class="w-full vicio-gradient text-white font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 hover:opacity-90 transition-opacity"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                Escanear QR de la fiesta
            </a>

            {{-- Ir a chats --}}
            <a
                href="{{ route('chats.index') }}"
                wire:navigate
                class="w-full bg-zinc-900 border border-zinc-800 text-white font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 hover:border-zinc-700 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-vicio-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Mis chats
                @if($unread > 0)
                    <span class="ml-auto size-5 rounded-full bg-vicio text-white text-xs font-bold flex items-center justify-center">{{ $unread }}</span>
                @endif
            </a>
        </div>

        {{-- Fiesta activa si existe --}}
        @if($currentParty = auth()->user()->currentParty)
            <div class="w-full max-w-sm bg-vicio-900/30 border border-vicio-700/50 rounded-2xl p-4 flex items-center gap-4">
                <div class="size-10 rounded-full vicio-gradient flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 fill-white" viewBox="0 0 24 24">
                        <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold text-sm truncate">{{ $currentParty->name }}</p>
                    <p class="text-vicio-300 text-xs">Fiesta activa</p>
                </div>
                <a
                    href="{{ route('party.' . ($currentParty->status === 'active' ? 'swipe' : 'waiting'), $currentParty->qr_code) }}"
                    wire:navigate
                    class="shrink-0 text-vicio-300 text-sm font-semibold hover:text-vicio-200 transition-colors"
                >
                    Entrar →
                </a>
            </div>
        @endif

    </div>
</x-layouts::app.sidebar>
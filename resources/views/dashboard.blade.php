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
    <flux:main class="min-h-screen bg-zinc-950">
        <div class="max-w-lg mx-auto px-4 py-10 flex flex-col items-center justify-center gap-8 text-center">

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
                    <p class="text-zinc-400 mt-1 text-sm">Bienvenido a VicioApp</p>
                </div>
            </div>

            {{-- Estado: sin fiesta activa --}}
            <div class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex flex-col items-center gap-4">
                <flux:icon.ticket class="size-10 text-zinc-600" />
                <div>
                    <p class="text-white font-medium">Sin fiesta activa</p>
                    <p class="text-zinc-500 text-sm mt-1">Escanea el QR de tu fiesta para unirte</p>
                </div>
                <a
                    href="#"
                    class="w-full bg-vicio text-white font-semibold text-center py-3 rounded-xl hover:bg-vicio-600 transition-colors text-sm"
                >
                    Escanear QR
                </a>
            </div>

            {{-- Info card --}}
            <div class="w-full grid grid-cols-3 gap-3">
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 flex flex-col items-center gap-2">
                    <flux:icon.heart class="size-6 text-vicio-400" />
                    <span class="text-white font-bold text-lg">0</span>
                    <span class="text-zinc-500 text-xs">Matches</span>
                </div>
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 flex flex-col items-center gap-2">
                    <flux:icon.chat-bubble-left-right class="size-6 text-vicio-400" />
                    <span class="text-white font-bold text-lg">0</span>
                    <span class="text-zinc-500 text-xs">Chats</span>
                </div>
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 flex flex-col items-center gap-2">
                    <flux:icon.fire class="size-6 text-vicio-400" />
                    <span class="text-white font-bold text-lg">0</span>
                    <span class="text-zinc-500 text-xs">Likes</span>
                </div>
            </div>

        </div>
    </flux:main>
</x-layouts::app.sidebar>
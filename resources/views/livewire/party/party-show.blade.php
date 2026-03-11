<div
    class="min-h-screen bg-zinc-950 flex flex-col items-center justify-center px-6 py-10 text-white"
    wire:poll.30s="checkStatus"
>
    <div class="w-full max-w-sm flex flex-col items-center gap-6 text-center">

        @if($party->cover_image)
            <img src="{{ asset('storage/' . $party->cover_image) }}"
                 class="w-full h-48 object-cover rounded-3xl" />
        @else
            <div class="size-20 rounded-full vicio-gradient flex items-center justify-center shadow-xl">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-10 fill-white">
                    <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
                </svg>
            </div>
        @endif

        <div>
            <h1 class="text-2xl font-bold">{{ $party->name }}</h1>
            @if($party->location)
                <p class="text-zinc-400 text-sm mt-1">{{ $party->location }}</p>
            @endif
            @if($party->starts_at)
                <p class="text-zinc-500 text-xs mt-1">{{ $party->starts_at->format('d/m/Y · H:i') }}</p>
            @endif
        </div>

        @if($party->description)
            <p class="text-zinc-400 text-sm leading-relaxed">{{ $party->description }}</p>
        @endif

        @php
            $statusConfig = [
                'registration' => ['text' => 'Registro abierto', 'class' => 'bg-green-900/50 text-green-400'],
                'active'       => ['text' => '¡En marcha!',      'class' => 'bg-vicio-900/50 text-vicio-300'],
                'finished'     => ['text' => 'Finalizada',       'class' => 'bg-zinc-800 text-zinc-500'],
            ];
            $config = $statusConfig[$party->status] ?? ['text' => 'Próximamente', 'class' => 'bg-zinc-800 text-zinc-400'];
        @endphp

        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
            {{ $config['text'] }}
        </span>

        <p class="text-zinc-500 text-sm">
            {{ $attendees }} personas ya se han unido
        </p>

        {{-- CUENTA ATRÁS --}}
        @if($party->starts_at && $party->status === 'registration')
            <div
                class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-6"
                x-data="{
                    seconds: {{ $secondsLeft }},
                    get h() { return String(Math.floor(this.seconds / 3600)).padStart(2,'0') },
                    get m() { return String(Math.floor((this.seconds % 3600) / 60)).padStart(2,'0') },
                    get s() { return String(this.seconds % 60).padStart(2,'0') },
                    tick() {
                        if (this.seconds > 0) {
                            this.seconds--;
                            setTimeout(() => this.tick(), 1000);
                        }
                    }
                }"
                x-init="tick()"
                x-on:livewire:navigated.window="seconds = {{ $secondsLeft }}; tick()"
            >
                <p class="text-zinc-400 text-xs mb-4">La fiesta empieza en</p>
                <div class="flex items-center justify-center gap-2">
                    <div class="flex flex-col items-center">
                        <span x-text="h" class="font-mono text-3xl font-bold text-white tabular-nums"></span>
                        <span class="text-zinc-600 text-xs mt-1">h</span>
                    </div>
                    <span class="font-mono text-3xl font-bold text-vicio-400 pb-4">:</span>
                    <div class="flex flex-col items-center">
                        <span x-text="m" class="font-mono text-3xl font-bold text-white tabular-nums"></span>
                        <span class="text-zinc-600 text-xs mt-1">min</span>
                    </div>
                    <span class="font-mono text-3xl font-bold text-vicio-400 pb-4">:</span>
                    <div class="flex flex-col items-center">
                        <span x-text="s" class="font-mono text-3xl font-bold text-white tabular-nums"></span>
                        <span class="text-zinc-600 text-xs mt-1">seg</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- BOTÓN DE ACCIÓN --}}
        <div class="w-full space-y-3">
            @if($party->status === 'finished')
                <p class="text-center text-zinc-500 text-sm">Esta fiesta ha finalizado.</p>
            @elseif(in_array($party->status, ['registration', 'active']))
                <a
                    href="{{ route('party.register', $party->qr_code) }}"
                    class="w-full vicio-gradient text-white font-semibold text-center py-4 rounded-2xl block hover:opacity-90 transition-opacity text-base"
                >
                    🎉 ¡Quiero entrar!
                </a>
            @endif

            @auth
                <p class="text-zinc-600 text-xs">
                    Entrando como <span class="text-zinc-400">{{ auth()->user()->name }}</span>
                </p>
            @endauth
        </div>

    </div>
</div>
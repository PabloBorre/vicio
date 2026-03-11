<div
    class="min-h-screen bg-zinc-950 flex flex-col items-center justify-center px-4 py-10"
    x-data="{
        seconds: @entangle('secondsLeft').live,
        formatted() {
            if (this.seconds <= 0) return '00:00:00';
            const h = Math.floor(this.seconds / 3600);
            const m = Math.floor((this.seconds % 3600) / 60);
            const s = this.seconds % 60;
            return [h,m,s].map(v => String(v).padStart(2,'0')).join(':');
        }
    }"
    wire:poll.5s="checkPartyStatus"
>

    {{-- Logo --}}
    <div class="flex flex-col items-center gap-2 mb-10">
        <div class="size-14 rounded-full vicio-gradient flex items-center justify-center shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-7 fill-white">
                <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
            </svg>
        </div>
        <h1 class="text-white font-bold text-xl">{{ $party->name }}</h1>
        <p class="text-zinc-500 text-sm">Sala de espera</p>
    </div>

    {{-- Countdown --}}
    <div class="w-full max-w-xs mb-10">
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 flex flex-col items-center gap-4">

            @if($secondsLeft > 0)
                <p class="text-zinc-400 text-sm">El swipe empieza en</p>

                <div
                    class="font-mono text-5xl font-bold text-white tracking-widest"
                    x-text="formatted()"
                    x-effect="if (seconds > 0) { setTimeout(() => seconds--, 1000) }"
                >
                    {{ gmdate('H:i:s', $secondsLeft) }}
                </div>

                <div class="w-full bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                    @php
                        $total = max(1, $party->starts_at->diffInSeconds($party->created_at));
                        $progress = min(100, max(0, (1 - $secondsLeft / $total) * 100));
                    @endphp
                    <div
                        class="h-full vicio-gradient rounded-full transition-all duration-1000"
                        style="width: {{ $progress }}%"
                    ></div>
                </div>
            @else
                <div class="text-center space-y-2">
                    <div class="text-4xl">🎉</div>
                    <p class="text-white font-semibold">¡La fiesta está arrancando!</p>
                    <p class="text-zinc-500 text-sm">Redirigiendo...</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Info adicional --}}
    <div class="w-full max-w-xs space-y-3">

        {{-- Asistentes --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-zinc-400 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Asistentes confirmados</span>
            </div>
            <span class="text-white font-bold">{{ $attendees }}</span>
        </div>

        {{-- Ubicación --}}
        @if($party->location)
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2 text-zinc-400 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Ubicación</span>
                </div>
                <span class="text-white text-sm font-medium">{{ $party->location }}</span>
            </div>
        @endif

        {{-- Tu perfil --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 flex items-center gap-3">
            @if(auth()->user()->profile_photo_path)
                <img
                    src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                    class="size-9 rounded-full object-cover"
                    alt="{{ auth()->user()->username }}"
                />
            @else
                <div class="size-9 rounded-full bg-vicio flex items-center justify-center text-white text-sm font-bold">
                    {{ auth()->user()->initials() }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate">{{ auth()->user()->username ?? auth()->user()->name }}</p>
                <p class="text-zinc-500 text-xs">Registrado ✓</p>
            </div>
        </div>

        {{-- Tip --}}
        <p class="text-center text-zinc-600 text-xs pt-2">
            Esta página se actualizará automáticamente cuando empiece el swipe
        </p>
    </div>

</div>
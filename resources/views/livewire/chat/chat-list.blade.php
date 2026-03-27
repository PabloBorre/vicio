<div class="w-full max-w-[430px] mx-auto flex flex-col" style="height: 100dvh; background-color: #A678C8;">

    {{-- ── HEADER ── --}}
    @php
        $currentParty = auth()->user()->parties()
            ->whereIn('parties.status', ['registration', 'active'])
            ->latest('pivot_joined_at')
            ->first();
        $backRoute = $currentParty
            ? ($currentParty->status === 'active'
                ? route('party.swipe', $currentParty->qr_code)
                : route('party.waiting', $currentParty->qr_code))
            : route('parties');
    @endphp

    <div class="relative z-50 shrink-0 flex items-center justify-between px-4 py-3" style="background-color: #  ;">

        {{-- Flecha atrás (izquierda) --}}
        <a href="{{ $backRoute }}" wire:navigate
           class="size-11 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        {{-- Logo centro --}}
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/Logo.png') }}" alt="VicioApp" width="48" height="48">
            <span class="text-white font-bold text-2xl tracking-tight">VicioApp</span>
        </div>

        {{-- Botón menú (derecha) --}}
        <div class="relative" x-data="{ open: false }">
            <button
                @click.stop="open = !open"
                class="size-11 rounded-full flex items-center justify-center hover:bg-white/90 transition-colors" style="background-color: #2D0A4E;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="#A678C8">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
</svg>
            </button>

            {{-- Overlay --}}
            <div
                x-show="open"
                @click="open = false"
                class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm"
                style="display: none;"
            ></div>

            {{-- Dropdown --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                class="absolute right-0 top-14 z-50 flex flex-col gap-2 min-w-[200px]"
                style="display: none;"
            >
                <a href="{{ route('profile.edit') }}"
                    wire:navigate
                    class="w-full text-center px-6 py-4 rounded-2xl font-semibold text-lg whitespace-nowrap shadow-lg"
                    style="background: #f5f0eb; color: #49197C;"
                >
                    Mi perfil
                </a>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full text-center px-6 py-4 rounded-2xl font-semibold text-lg whitespace-nowrap shadow-lg"
                        style="background: #f5f0eb; color: #49197C;"
                    >
                        Salir ✕
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── LISTA DE CHATS ── --}}
    <div class="flex-1 overflow-y-auto px-4 py-4 flex flex-col gap-3">

        @forelse($matches as $match)
            
        <a href="{{ route('chats.show', $match['match_id']) }}"
    wire:navigate
    class="flex items-center gap-4 transition-opacity hover:opacity-90 active:opacity-75"
    style="background-color: #2D0A4E; border-radius: 9999px; padding: 10px 20px 10px 10px;"
>
    {{-- Avatar --}}
    <div class="relative shrink-0">
        <img
            src="{{ $match['profile_photo_url'] }}"
            alt="{{ $match['username'] }}"
            style="width: 50px; height: 52px; border-radius: 9999px;"
        />
        @if($match['unread'] > 0)
            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center text-white font-bold"
                style="width: 18px; height: 18px; border-radius: 9999px; background-color: #49197C; font-size: 10px;">
                {{ $match['unread'] }}
            </span>
        @endif
    </div>

    {{-- Info --}}
<div class="flex-1 min-w-0">
    <p class="font-bold text-white" style="font-size: 18px;">{{ $match['username'] }}</p>
    <div class="flex items-center justify-between gap-2 mt-0.5">
        <p class="text-sm truncate {{ $match['unread'] > 0 ? 'font-semibold text-white' : '' }}"
            style="{{ $match['unread'] > 0 ? '' : 'color: rgba(255,255,255,0.55);' }}">
            {{ $match['last_message'] ?? 'Di algo bonito 👋' }}
        </p>
        @if($match['last_message_time'])
            <span class="shrink-0 text-xs" style="color: rgba(255,255,255,0.45);">
                {{ $match['last_message_time'] }}
            </span>
        @endif
    </div>
</div>

</a>
        @empty
            <div class="flex flex-col items-center justify-center flex-1 text-center gap-4 px-6 py-20">
                <div class="text-5xl">💫</div>
                <p class="font-semibold text-white text-base">Aún no tienes matches</p>
                <p class="text-sm" style="color: rgba(255,255,255,0.65);">Cuando hagas match en una fiesta, los chats aparecerán aquí</p>
            </div>
        @endforelse

    </div>

</div>
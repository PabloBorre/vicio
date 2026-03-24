<div class="w-full max-w-[430px] mx-auto flex flex-col" style="height: 100dvh; background-color: #A678C8;">

    {{-- Header --}}
    <div class="shrink-0 flex items-center justify-between px-4 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
        <div>
            <h1 class="text-white font-bold text-xl">Chats</h1>
            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.6);">Tus matches de la noche</p>
        </div>
        <a href="{{ route('dashboard') }}" wire:navigate
            class="size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
            style="background-color: rgba(255,255,255,0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
    </div>

    {{-- Lista --}}
    <div class="flex-1 overflow-y-auto">
        @forelse($matches as $match)
            <a
                href="{{ route('chats.show', $match['match_id']) }}"
                wire:navigate
                class="flex items-center gap-3 px-4 py-3.5 transition-colors"
                style="border-bottom: 1px solid rgba(255,255,255,0.12);"
                onmouseenter="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                onmouseleave="this.style.backgroundColor='transparent'"
            >
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <img
                        src="{{ $match['profile_photo_url'] }}"
                        class="size-13 rounded-full object-cover"
                        style="width:52px; height:52px; border-radius:9999px; object-fit:cover; outline: 2px solid rgba(255,255,255,0.3); outline-offset: 1px;"
                        alt="{{ $match['username'] }}"
                    />
                    @if($match['unread'] > 0)
                        <span class="absolute -top-0.5 -right-0.5 size-5 rounded-full text-white text-[10px] font-bold flex items-center justify-center"
                            style="background-color: #2d0a3e; width:18px; height:18px; font-size:10px;">
                            {{ $match['unread'] }}
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-semibold text-sm truncate text-white">{{ $match['username'] }}</p>
                        @if($match['last_message_time'])
                            <span class="text-xs shrink-0" style="color: rgba(255,255,255,0.5);">{{ $match['last_message_time'] }}</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between gap-2 mt-0.5">
                        <p class="text-xs truncate {{ $match['unread'] > 0 ? 'font-semibold text-white' : '' }}"
                            style="{{ $match['unread'] > 0 ? '' : 'color: rgba(255,255,255,0.6);' }}">
                            {{ $match['last_message'] ?? 'Di algo bonito 👋' }}
                        </p>
                    </div>
                </div>

                {{-- Chevron --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: rgba(255,255,255,0.3);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-32 text-center gap-4 px-6">
                <div class="text-5xl">💫</div>
                <p class="font-semibold text-white text-base">Aún no tienes matches</p>
                <p class="text-sm" style="color: rgba(255,255,255,0.6);">Cuando hagas match en una fiesta, los chats aparecerán aquí</p>
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="mt-2 px-6 py-3 rounded-2xl font-semibold text-white transition-opacity hover:opacity-90"
                    style="background-color: #2d0a3e;">
                    Volver al inicio
                </a>
            </div>
        @endforelse
    </div>

</div>
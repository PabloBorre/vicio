<div class="h-full flex flex-col bg-zinc-950">

    {{-- Header --}}
    <div class="shrink-0 border-b border-zinc-800 px-4 py-4">
        <h1 class="text-white font-bold text-lg">Chats</h1>
        <p class="text-zinc-500 text-xs mt-0.5">Tus matches de la noche</p>
    </div>

    {{-- Lista --}}
    <div class="flex-1 overflow-y-auto divide-y divide-zinc-800">
        @forelse($matches as $match)
            <a
                href="{{ route('chats.show', $match['match_id']) }}"
                wire:navigate
                class="flex items-center gap-3 px-4 py-3 hover:bg-zinc-800/50 transition-colors"
            >
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <img
                        src="{{ $match['profile_photo_url'] }}"
                        class="size-12 rounded-full object-cover"
                        alt="{{ $match['username'] }}"
                    />
                    @if($match['unread'] > 0)
                        <span class="absolute -top-0.5 -right-0.5 size-4 rounded-full bg-vicio-400 text-white text-[10px] font-bold flex items-center justify-center">
                            {{ $match['unread'] }}
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-white font-semibold text-sm truncate">{{ $match['username'] }}</p>
                        @if($match['last_message_time'])
                            <span class="text-zinc-600 text-xs shrink-0">{{ $match['last_message_time'] }}</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between gap-2 mt-0.5">
                        <p class="text-zinc-500 text-xs truncate">
                            {{ $match['last_message'] ?? 'Di algo bonito 👋' }}
                        </p>
                        @if($match['party_name'])
                            <span class="text-zinc-700 text-[10px] shrink-0">{{ $match['party_name'] }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center h-full py-20 text-center gap-3 px-4">
                <div class="text-4xl">💫</div>
                <p class="text-zinc-400 font-medium text-sm">Aún no tienes matches</p>
                <p class="text-zinc-600 text-xs">Cuando hagas match en una fiesta, los chats aparecerán aquí</p>
            </div>
        @endforelse
    </div>

</div>

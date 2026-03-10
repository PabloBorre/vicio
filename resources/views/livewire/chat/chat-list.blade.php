<div class="min-h-screen bg-zinc-950">

    {{-- Header --}}
    <div class="sticky top-0 z-10 bg-zinc-950/95 backdrop-blur-sm border-b border-zinc-800 px-4 py-4">
        <div class="max-w-lg mx-auto flex items-center justify-between">
            <div>
                <h1 class="text-white font-bold text-xl">Chats</h1>
                <p class="text-zinc-500 text-xs">{{ $matches->count() }} conversaciones</p>
            </div>
        </div>
    </div>

    <div class="max-w-lg mx-auto px-4 py-4">

        @if($matches->isEmpty())
            {{-- Sin matches --}}
            <div class="flex flex-col items-center gap-4 text-center py-20">
                <div class="size-20 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-lg">Sin conversaciones</p>
                    <p class="text-zinc-500 text-sm mt-1">Cuando hagas match podrás chatear</p>
                </div>
            </div>
        @else
            <div class="space-y-1">
                @foreach($matches as $match)
                    <a
                        href="{{ route('chat.show', $match['match_id']) }}"
                        wire:navigate
                        class="flex items-center gap-4 p-3 rounded-2xl hover:bg-zinc-900 transition-colors group"
                    >
                        {{-- Avatar --}}
                        <div class="relative shrink-0">
                            <img
                                src="{{ $match['profile_photo_url'] }}"
                                class="size-14 rounded-full object-cover"
                                alt="{{ $match['username'] }}"
                            />
                            @if($match['unread'] > 0)
                                <div class="absolute -top-0.5 -right-0.5 size-5 rounded-full bg-vicio flex items-center justify-center">
                                    <span class="text-white text-[10px] font-bold">{{ $match['unread'] }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-white font-semibold truncate">{{ $match['username'] }}</span>
                                @if($match['last_message_time'])
                                    <span class="text-zinc-600 text-xs shrink-0">{{ $match['last_message_time'] }}</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                <p @class([
                                    'text-sm truncate',
                                    'text-white font-medium' => $match['unread'] > 0,
                                    'text-zinc-500'          => $match['unread'] === 0,
                                ])>
                                    {{ $match['last_message'] ?? 'Di hola 👋' }}
                                </p>
                                <span class="text-zinc-600 text-xs shrink-0">{{ $match['party_name'] }}</span>
                            </div>
                        </div>

                        {{-- Flecha --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-700 group-hover:text-zinc-500 shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
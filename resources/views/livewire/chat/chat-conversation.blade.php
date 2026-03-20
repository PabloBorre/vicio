<div
    class="flex flex-col"
    style="height: 100dvh; background-color: #7B3DAA;"
    x-data="{ showProfile: false }"
    x-on:scroll-to-bottom.window="
        $nextTick(() => {
            const el = document.getElementById('messages-container');
            if (el) el.scrollTop = el.scrollHeight;
        })
    "
>
    {{-- ══════════════════════════════════════════ --}}
    {{-- HEADER                                     --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="shrink-0 px-5 py-4" style="background-color: #4A1A6B;">
        <div class="flex items-center justify-between">

            {{-- Flecha atrás --}}
            <a href="{{ route('chats.index') }}" wire:navigate class="text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            {{-- Nombre + edad centrado --}}
            <button type="button" @click="showProfile = true" class="flex items-center gap-2">
                <span class="text-white font-bold text-lg">{{ $other->username ?? $other->name }}</span>
                @if($other->age)
                    <span class="text-white font-light text-lg">{{ $other->age }}</span>
                @endif
            </button>

            {{-- Avatar derecha --}}
            <button type="button" @click="showProfile = true">
                <img
                    src="{{ $other->profile_photo_url }}"
                    class="size-14 rounded-full object-cover"
                    alt="{{ $other->username ?? $other->name }}"
                />
            </button>

        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- MENSAJES                                   --}}
    {{-- ══════════════════════════════════════════ --}}
    <div
        id="messages-container"
        class="flex-1 overflow-y-auto px-4 py-6 space-y-4"
        x-init="$el.scrollTop = $el.scrollHeight"
    >
        @if(empty($messages))
            <div class="flex flex-col items-center gap-3 py-10 text-center">
                <div class="text-4xl">👋</div>
                <p class="text-purple-200 text-sm">¡Hicisteis match! Di algo bonito.</p>
            </div>
        @endif

        @foreach($messages as $message)
    <div
        wire:key="msg-{{ $message['id'] }}"
        class="flex {{ $message['mine'] ? 'justify-end' : 'justify-start' }}"
    >
        <div class="max-w-[75%] space-y-1">
            <div style="
                background-color: #C8A8DC;
                color: #2D0A4E;
                padding: 14px 22px;
                font-size: 16px;
                line-height: 1.4;
                border-radius: {{ $message['mine'] ? '24px 24px 6px 24px' : '24px 24px 24px 6px' }};
            ">
                {{ $message['body'] }}
            </div>
            <p class="text-purple-300 text-[11px] {{ $message['mine'] ? 'text-right' : 'text-left' }} px-2">
                {{ $message['created_at'] }}
            </p>
        </div>
    </div>
@endforeach
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- INPUT                                      --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="shrink-0 px-4 py-4" style="background-color: #4A1A6B;">
        <form wire:submit="sendMessage" class="flex items-center gap-3">

            <input
                wire:model="body"
                type="text"
                maxlength="1000"
                placeholder="Escribe aquí tu mensaje"
                style="background-color: white; border:none; border-radius:9999px; font-size:16px; padding:16px 24px; color:#2D0A4E; outline:none; flex:1;"
            />

            <button
                type="submit"
                style="background-color: white; border:none; border-radius:9999px; width:56px; height:56px; display:flex; align-items:center; justify-content:center; cursor:pointer; shrink:0;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:22px; height:22px; fill:#4A1A6B;">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>

        </form>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- OVERLAY PERFIL                             --}}
    {{-- ══════════════════════════════════════════ --}}
    <div
        x-show="showProfile"
        x-transition
        class="absolute inset-0 z-50 flex flex-col"
        style="display:none; background-color: #4A1A6B;"
    >
        {{-- Foto grande --}}
        <div class="relative flex-1">
            <img
                src="{{ $other->profile_photo_url }}"
                class="w-full h-full object-cover"
                alt="{{ $other->username ?? $other->name }}"
            />
            <button
                type="button"
                @click="showProfile = false"
                class="absolute top-4 left-4 size-9 rounded-full flex items-center justify-center text-white"
                style="background-color: rgba(0,0,0,0.5);"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- Panel datos --}}
        <div class="shrink-0 px-5 pt-5 pb-8 space-y-4" style="background-color: #4A1A6B;">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-white text-2xl font-bold">
                        {{ $other->username ?? $other->name }}
                        @if($other->age)
                            <span class="text-purple-300 font-light">, {{ $other->age }}</span>
                        @endif
                    </h2>
                    @if($match->party)
                        <p class="text-purple-400 text-xs mt-1">🎉 {{ $match->party->name }}</p>
                    @endif
                </div>
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold text-purple-200" style="background-color: rgba(255,255,255,0.1);">
                    ✨ Match
                </span>
            </div>
            @if($other->bio)
                <p class="text-purple-200 text-sm leading-relaxed">{{ $other->bio }}</p>
            @endif
        </div>
    </div>

</div>
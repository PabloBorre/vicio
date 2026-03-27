<div
    class="flex flex-col relative"
    style="height: 100dvh; background-color: #7B3DAA; max-width: 430px; margin: 0 auto; width: 100%;"
    x-data="{ showProfile: false }"
    x-on:scroll-to-bottom.window="
        $nextTick(() => {
            const el = document.getElementById('messages-container');
            if (el) el.scrollTop = el.scrollHeight;
        })
    "
>
    {{-- HEADER --}}
    <div class="shrink-0 px-5 py-4" style="background-color: #4A1A6B;">
        <div class="flex items-center justify-between">
            <a href="{{ route('chats.index') }}" wire:navigate class="text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <button type="button" @click="showProfile = true" class="flex items-center gap-2">
                <span class="text-white font-bold text-lg">{{ $other->username ?? $other->name }}</span>
                @if($other->age)
                    <span class="text-white font-light text-lg">{{ $other->age }}</span>
                @endif
            </button>
            <button type="button" @click="showProfile = true">
                <img src="{{ $other->profile_photo_url }}" class="size-14 rounded-full object-cover" alt="{{ $other->username ?? $other->name }}" />
            </button>
        </div>
    </div>

    {{-- MENSAJES --}}
    <div id="messages-container" class="flex-1 overflow-y-auto px-4 py-6 space-y-4" x-init="$el.scrollTop = $el.scrollHeight">
        @if(empty($messages))
            <div class="flex flex-col items-center gap-3 py-10 text-center">
                <div class="text-4xl">👋</div>
                <p class="text-purple-200 text-sm">¡Hicisteis match! Di algo bonito.</p>
            </div>
        @endif
        @foreach($messages as $message)
            <div wire:key="msg-{{ $message['id'] }}" class="flex {{ $message['mine'] ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[75%] space-y-1">
                    <div style="background-color: #C8A8DC; color: #2D0A4E; padding: 14px 22px; font-size: 16px; line-height: 1.4; border-radius: {{ $message['mine'] ? '24px 24px 6px 24px' : '24px 24px 24px 6px' }};">
                        {{ $message['body'] }}
                    </div>
                    <p class="text-purple-300 text-[11px] {{ $message['mine'] ? 'text-right' : 'text-left' }} px-2">
                        {{ $message['created_at'] }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- INPUT --}}
    <div class="shrink-0 px-4 py-4" style="background-color: #4A1A6B;">
        <form wire:submit="sendMessage" class="flex items-center gap-3">
            <input wire:model="body" type="text" maxlength="1000" placeholder="Escribe aquí tu mensaje"
                style="background-color: white; border:none; border-radius:9999px; font-size:16px; padding:16px 24px; color:#2D0A4E; outline:none; flex:1;" />
            <button type="submit"
                style="background-color: white; border:none; border-radius:9999px; width:56px; height:56px; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:22px; height:22px; fill:#4A1A6B;">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </form>
    </div>

    {{-- OVERLAY PERFIL --}}
<div
    x-show="showProfile"
    x-transition
    class="absolute inset-0 z-50 flex flex-col"
    style="display:none; background-color: #4A1A6B;"
>
    {{-- Foto --}}
    <div class="relative flex-1" style="min-height: 160px;">
        <img src="{{ $other->profile_photo_url }}" class="w-full h-full object-cover" alt="{{ $other->username ?? $other->name }}" />
        <div class="absolute inset-x-0 bottom-0 h-32" style="background: linear-gradient(to top, #4A1A6B, transparent);"></div>
        <button type="button" @click="showProfile = false"
            class="absolute top-4 left-4 size-9 rounded-full flex items-center justify-center text-white"
            style="background-color: rgba(0,0,0,0.5);">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    {{-- Óvalo nombre --}}
    <div class="relative px-4 shrink-0" style="z-index: 10; margin-top: -36px;">
        <div class="flex items-center gap-4" style="background-color: #2D0A4E; border-radius: 9999px; padding: 10px 24px 10px 10px;">
            <img src="{{ $other->profile_photo_url }}" alt="{{ $other->username ?? $other->name }}"
                style="width: 48px; height: 48px; border-radius: 9999px; object-fit: cover; outline: 2px solid rgba(255,255,255,0.3); outline-offset: 1px; flex-shrink: 0;" />
            <div class="min-w-0">
                <p class="text-white font-bold truncate" style="font-size: 18px;">
                    {{ $other->username ?? $other->name }}
                    @if($other->age)
                        <span class="font-light text-purple-300">, {{ $other->age }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Bio --}}
    <div class="px-5 pt-5 pb-16 shrink-0" style="background-color: #4A1A6B;">
        @if($other->bio)
            <p class="text-purple-200 leading-relaxed" style="font-size: 15px;">{{ $other->bio }}</p>
        @else
            <p class="text-purple-400 text-sm italic">Sin descripción.</p>
        @endif
    </div>

</div>
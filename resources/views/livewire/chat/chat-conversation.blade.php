<div
    class="flex flex-col bg-zinc-950"
    style="height: 100dvh;"
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
    <div class="shrink-0 bg-zinc-950/95 backdrop-blur-sm border-b border-zinc-800/60 px-4 py-3">
        <div class="max-w-lg mx-auto flex items-center gap-3">

            {{-- Botón atrás --}}
            <a href="{{ route('chats.index') }}" wire:navigate
               class="size-9 rounded-full bg-zinc-800/80 flex items-center justify-center shrink-0 hover:bg-zinc-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            {{-- Avatar clickable → abre tarjeta de perfil --}}
            <button
                type="button"
                @click="showProfile = true"
                class="relative shrink-0 group"
                title="Ver perfil"
            >
                <img
                    src="{{ $other->profile_photo_url }}"
                    class="size-10 rounded-full object-cover ring-2 ring-transparent group-hover:ring-vicio-400 transition-all duration-200"
                    alt="{{ $other->username ?? $other->name }}"
                />
                {{-- Pulse indicator --}}
                <span class="absolute -bottom-0.5 -right-0.5 size-3 rounded-full bg-emerald-400 border-2 border-zinc-950"></span>
            </button>

            {{-- Nombre + fiesta --}}
            <div class="flex-1 min-w-0 cursor-pointer" @click="showProfile = true">
                <p class="text-white font-semibold truncate leading-tight hover:text-vicio-300 transition-colors">
                    {{ $other->username ?? $other->name }}
                </p>
                <p class="text-zinc-500 text-xs truncate">{{ $match->party->name ?? 'VicioApp' }}</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- MENSAJES                                   --}}
    {{-- ══════════════════════════════════════════ --}}
    <div
        id="messages-container"
        class="flex-1 overflow-y-auto px-4 py-4 space-y-2"
        x-init="$el.scrollTop = $el.scrollHeight"
    >
        <div class="max-w-lg mx-auto space-y-2">

            @if(empty($messages))
                <div class="flex flex-col items-center gap-3 py-10 text-center">
                    <div class="text-4xl">👋</div>
                    <p class="text-zinc-400 text-sm">¡Hicisteis match! Di algo bonito.</p>
                </div>
            @endif

            @foreach($messages as $message)
                <div
                    wire:key="msg-{{ $message['id'] }}"
                    class="flex {{ $message['mine'] ? 'justify-end' : 'justify-start' }}"
                >
                    <div class="max-w-[75%] space-y-1">
                        <div @class([
                            'px-4 py-2.5 rounded-2xl text-sm leading-relaxed',
                            'vicio-gradient text-white rounded-br-sm' => $message['mine'],
                            'bg-zinc-800 text-zinc-100 rounded-bl-sm' => !$message['mine'],
                        ])>
                            {{ $message['body'] }}
                        </div>
                        <p class="text-zinc-600 text-[11px] {{ $message['mine'] ? 'text-right' : 'text-left' }} px-1">
                            {{ $message['created_at'] }}
                        </p>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- INPUT                                      --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="shrink-0 border-t border-zinc-800 bg-zinc-950 px-4 py-3 pb-safe">
        <div class="max-w-lg mx-auto">
            <form wire:submit="sendMessage" class="flex items-end gap-2">
                <div class="flex-1 bg-zinc-900 border border-zinc-700 rounded-2xl px-4 py-2.5 focus-within:border-vicio-400 transition-colors">
                    <textarea
                        wire:model="body"
                        rows="1"
                        maxlength="1000"
                        placeholder="Escribe un mensaje..."
                        class="w-full bg-transparent text-white placeholder-zinc-600 resize-none focus:outline-none leading-relaxed"
                        style="font-size: 16px;"
                        x-data="{}"
                        x-on:keydown.enter.prevent="
                            if (!$event.shiftKey) { $wire.sendMessage(); }
                        "
                        @input="
                            $el.style.height = 'auto';
                            $el.style.height = Math.min($el.scrollHeight, 120) + 'px';
                        "
                    ></textarea>
                </div>
                <button
                    type="submit"
                    class="size-11 rounded-full vicio-gradient flex items-center justify-center shrink-0 hover:opacity-90 active:scale-95 transition-all disabled:opacity-40"
                    wire:loading.attr="disabled"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PANEL DE PERFIL                                                   --}}
    {{-- Foto entera arriba · panel de datos sólido abajo                  --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showProfile"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full"
        class="absolute inset-0 z-50 flex flex-col bg-zinc-950"
        style="display: none;"
    >

        {{-- ── FOTO: ocupa el espacio restante, sin recortar ── --}}
        <div class="relative flex-1 bg-black flex items-center justify-center min-h-0">

            <img
                src="{{ $other->profile_photo_url }}"
                class="w-full h-full object-contain"
                alt="{{ $other->username ?? $other->name }}"
            />

            {{-- Botón cerrar --}}
            <button
                type="button"
                @click="showProfile = false"
                class="absolute top-4 left-4 size-9 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center text-white hover:bg-black/70 transition-all"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- ── PANEL DE DATOS: fondo sólido, altura fija ── --}}
        <div class="shrink-0 bg-zinc-900 border-t border-zinc-800 px-5 pt-5 pb-8 space-y-4">

            {{-- Nombre + edad + fiesta --}}
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="text-white text-2xl font-bold leading-tight truncate">
                        {{ $other->username ?? $other->name }}
                        @if($other->age)
                            <span class="text-zinc-400 font-normal text-xl">, {{ $other->age }}</span>
                        @endif
                    </h2>
                    @if($match->party)
                        <p class="text-zinc-500 text-xs flex items-center gap-1 mt-1">
                            🎉 {{ $match->party->name }}
                        </p>
                    @endif
                </div>

                {{-- Badge match --}}
                <span class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-vicio-500/20 border border-vicio-500/40 text-vicio-300 text-xs font-semibold">
                    ✨ Match
                </span>
            </div>

{{-- Chips género / preferencia --}}
            <div class="flex flex-wrap gap-2">
                @if($other->gender_identity)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-800 border border-zinc-700 text-zinc-300 text-xs font-medium">
                        @if($other->gender_identity === 'man')
                            <svg class="size-3.5 shrink-0 fill-blue-400" viewBox="0 0 240.699 240.699" xmlns="http://www.w3.org/2000/svg"><path d="M240.625,13.726c-0.021-0.217-0.063-0.428-0.094-0.642c-0.038-0.27-0.069-0.541-0.123-0.809c-0.049-0.25-0.118-0.493-0.18-0.738c-0.058-0.228-0.107-0.458-0.176-0.684c-0.074-0.244-0.166-0.478-0.251-0.716c-0.08-0.223-0.153-0.448-0.245-0.668c-0.093-0.225-0.202-0.441-0.306-0.66c-0.107-0.224-0.205-0.45-0.322-0.669c-0.114-0.214-0.245-0.417-0.37-0.625c-0.127-0.212-0.246-0.426-0.384-0.633c-0.158-0.236-0.333-0.459-0.504-0.686c-0.124-0.164-0.237-0.333-0.368-0.493c-0.63-0.768-1.334-1.471-2.102-2.101c-0.151-0.125-0.313-0.232-0.468-0.35c-0.234-0.178-0.466-0.359-0.712-0.523c-0.196-0.131-0.4-0.245-0.602-0.366c-0.219-0.131-0.433-0.268-0.658-0.389c-0.206-0.11-0.418-0.203-0.628-0.303c-0.233-0.111-0.464-0.227-0.703-0.326c-0.202-0.083-0.409-0.15-0.614-0.225c-0.256-0.093-0.51-0.191-0.771-0.27c-0.201-0.061-0.405-0.104-0.607-0.157c-0.271-0.069-0.54-0.144-0.816-0.199c-0.226-0.044-0.453-0.069-0.68-0.104c-0.256-0.039-0.51-0.086-0.771-0.111c-0.386-0.038-0.772-0.051-1.16-0.059C228.916,0.002,228.815,0,228.709,0H178.34c-8.284,0-15,6.716-15,15s6.716,15,15,15h11.148L166.89,52.806C149.445,39.679,127.767,31.888,104.303,31.888C46.79,31.888,0,78.678,0,136.19c0,57.512,46.79,104.302,104.303,104.302c57.512,0,104.301-46.79,104.301-104.302c0-23.251-7.65-44.748-20.561-62.111l22.657-22.658v11.146c0,8.284,6.716,15,15,15c8.284,0,15-6.716,15-15V15.213C240.699,14.716,240.674,14.22,240.625,13.726z M104.303,210.492C63.332,210.492,30,177.161,30,136.19c0-40.971,33.332-74.303,74.303-74.303c40.97,0,74.301,33.332,74.301,74.303C178.604,177.161,145.273,210.492,104.303,210.492z"/></svg>
                            Hombre
                        @elseif($other->gender_identity === 'woman')
                            <svg class="size-3.5 shrink-0 fill-pink-400" viewBox="0 0 290.507 290.507" xmlns="http://www.w3.org/2000/svg"><path d="M249.555,104.303C249.555,46.79,202.765,0,145.253,0S40.952,46.79,40.952,104.303c0,52.419,38.872,95.923,89.301,103.219l0.001,19.497h-18.487c-8.284,0-15,6.716-15,15c0,8.284,6.716,15,15,15h18.487l0.001,18.489c0,8.284,6.716,15,15,15c8.284,0,15-6.716,15-15.001l-0.001-18.488h18.487c8.284,0,15-6.716,15-15c0-8.284-6.716-15-15-15h-18.487l-0.001-19.497C210.683,200.227,249.555,156.722,249.555,104.303z M70.952,104.303C70.952,63.332,104.284,30,145.253,30s74.302,33.332,74.302,74.303c0,40.97-33.332,74.302-74.302,74.302S70.952,145.273,70.952,104.303z"/></svg>
                            Mujer
                        @else
                            <svg class="size-3.5 shrink-0 fill-purple-400" viewBox="0 0 322.187 322.187" xmlns="http://www.w3.org/2000/svg"><path d="M281.439,14.934c-0.002-0.471-0.025-0.941-0.071-1.411c-0.023-0.236-0.067-0.466-0.101-0.699c-0.037-0.25-0.065-0.502-0.115-0.75c-0.052-0.264-0.124-0.52-0.19-0.778c-0.055-0.215-0.102-0.431-0.165-0.644c-0.077-0.254-0.172-0.5-0.262-0.748c-0.077-0.212-0.146-0.426-0.232-0.636c-0.098-0.235-0.212-0.462-0.321-0.691c-0.101-0.213-0.195-0.429-0.307-0.638c-0.121-0.226-0.258-0.44-0.39-0.659c-0.121-0.2-0.233-0.403-0.364-0.599c-0.167-0.25-0.353-0.487-0.534-0.727c-0.114-0.15-0.218-0.305-0.338-0.452c-0.631-0.77-1.336-1.476-2.106-2.106c-0.138-0.113-0.285-0.211-0.426-0.318c-0.248-0.189-0.494-0.381-0.754-0.554c-0.186-0.124-0.379-0.231-0.569-0.346c-0.229-0.139-0.455-0.282-0.691-0.409c-0.196-0.104-0.397-0.192-0.596-0.287c-0.244-0.117-0.485-0.237-0.736-0.341c-0.191-0.079-0.386-0.141-0.579-0.212c-0.268-0.098-0.533-0.2-0.807-0.282c-0.187-0.056-0.377-0.097-0.565-0.145c-0.285-0.074-0.568-0.152-0.859-0.21c-0.204-0.04-0.41-0.063-0.615-0.094c-0.278-0.043-0.553-0.093-0.836-0.121c-0.325-0.031-0.65-0.039-0.977-0.049C266.768,0.019,266.607,0,266.442,0h-47.359c-8.284,0-15,6.716-15,15c0,8.284,6.716,15,15,15h11.148l-22.598,22.598C190.188,39.471,168.51,31.68,145.046,31.68c-57.512,0-104.302,46.79-104.302,104.303c0,52.419,38.871,95.923,89.301,103.219l0.001,19.497h-18.487c-8.284,0-15,6.716-15,15c0,8.284,6.716,15,15,15h18.488l0.001,18.488c0,8.284,6.716,15,15,15c8.284,0,15-6.716,15-15.001l-0.001-18.487h18.487c8.284,0,15-6.716,15-15c0-8.284-6.716-15-15-15h-18.487l-0.001-19.497c50.43-7.295,89.302-50.8,89.302-103.219c0-23.251-7.65-44.748-20.562-62.111l22.656-22.657V62.36c0,8.284,6.716,15,15,15c8.284,0,15-6.716,15-15V15C281.442,14.978,281.439,14.957,281.439,14.934z M145.046,210.285c-40.97,0-74.302-33.332-74.302-74.302c0-40.971,33.331-74.303,74.302-74.303c40.97,0,74.302,33.332,74.302,74.303C219.348,176.953,186.016,210.285,145.046,210.285z"/></svg>
                            {{ ucfirst($other->gender_identity) }}
                        @endif
                    </span>
                @endif
 
                @if($other->sexual_preference)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-800 border border-zinc-700 text-zinc-300 text-xs font-medium">
                        @if($other->sexual_preference === 'hetero') 💛 Hetero
                        @elseif($other->sexual_preference === 'homo') 🏳️‍🌈 Homo
                        @elseif($other->sexual_preference === 'bi') 💜 Bi
                        @else {{ ucfirst($other->sexual_preference) }}
                        @endif
                    </span>
                @endif
            </div>

            {{-- Bio --}}
            @if($other->bio)
                <p class="text-zinc-400 text-sm leading-relaxed">{{ $other->bio }}</p>
            @endif

            {{-- Botón --}}
            <button
                type="button"
                @click="showProfile = false"
                class="w-full py-3.5 rounded-2xl vicio-gradient text-white font-semibold text-sm hover:opacity-90 active:scale-[0.98] transition-all"
            >
                💬 Seguir el chat
            </button>
        </div>

    </div>

</div>
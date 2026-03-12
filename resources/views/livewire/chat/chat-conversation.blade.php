<div
    class="flex flex-col bg-zinc-950"
    style="height: 100dvh;"
    x-data="{}"
    x-on:scroll-to-bottom.window="
        $nextTick(() => {
            const el = document.getElementById('messages-container');
            if (el) el.scrollTop = el.scrollHeight;
        })
    "
>
    {{-- Header --}}
    <div class="shrink-0 bg-zinc-950/95 backdrop-blur-sm border-b border-zinc-800 px-4 py-3">
        <div class="max-w-lg mx-auto flex items-center gap-3">

            {{-- Botón atrás --}}
            <a href="{{ route('chats.index') }}" wire:navigate class="size-9 rounded-full bg-zinc-800 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            {{-- Avatar + nombre --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <img
                    src="{{ $other->profile_photo_url }}"
                    class="size-10 rounded-full object-cover shrink-0"
                    alt="{{ $other->username ?? $other->name }}"
                />
                <div class="min-w-0">
                    <p class="text-white font-semibold truncate">{{ $other->username ?? $other->name }}</p>
                    <p class="text-zinc-500 text-xs">{{ $match->party->name ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes --}}
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

    {{-- Input de mensaje --}}
    <div class="shrink-0 border-t border-zinc-800 bg-zinc-950 px-4 py-3 pb-safe">
        <div class="max-w-lg mx-auto">
            <form
                wire:submit="sendMessage"
                class="flex items-end gap-2"
            >
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
                            if (!$event.shiftKey) {
                                $wire.sendMessage();
                            }
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

</div>
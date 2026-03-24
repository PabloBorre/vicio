<div
    class="relative flex flex-col select-none overflow-hidden"
    style="height: 100dvh; background-color: #470D4E;"
    x-data="{
        dragging: false,
        startX: 0,
        startY: 0,
        currentX: 0,
        currentY: 0,
        rotation: 0,
        opacity: 1,
        showLike: false,
        showNope: false,
        threshold: 80,
        showMatch: @entangle('lastMatch').live,
        showMenu: false,

        get cardStyle() {
            return `transform: translate(${this.currentX}px, ${this.currentY}px) rotate(${this.rotation}deg); opacity: ${this.opacity}; transition: ${this.dragging ? 'none' : 'transform 0.3s, opacity 0.3s'}`;
        },

        startDrag(e) {
            this.dragging = true;
            const point = e.touches ? e.touches[0] : e;
            this.startX = point.clientX;
            this.startY = point.clientY;
        },

        onDrag(e) {
            if (!this.dragging) return;
            const point = e.touches ? e.touches[0] : e;
            this.currentX = point.clientX - this.startX;
            this.currentY = (point.clientY - this.startY) * 0.3;
            this.rotation = this.currentX * 0.08;
            this.opacity = Math.max(0.6, 1 - Math.abs(this.currentX) / 300);
            this.showLike = this.currentX > 30;
            this.showNope = this.currentX < -30;
        },

        async endDrag() {
            if (!this.dragging) return;
            this.dragging = false;
            if (this.currentX > this.threshold) {
                await this.animateOut('right');
                this.reset();
                $wire.swipe('like');
            } else if (this.currentX < -this.threshold) {
                await this.animateOut('left');
                this.reset();
                $wire.swipe('dislike');
            } else {
                this.currentX = 0;
                this.currentY = 0;
                this.rotation = 0;
                this.opacity = 1;
            }
            this.showLike = false;
            this.showNope = false;
        },

        animateOut(dir) {
            return new Promise(resolve => {
                const target = dir === 'right' ? 500 : -500;
                this.currentX = target;
                this.currentY = 40;
                this.opacity = 0;
                setTimeout(resolve, 300);
            });
        },

        async triggerLike() {
            await this.animateOut('right');
            this.reset();
            $wire.swipe('like');
        },

        async triggerDislike() {
            await this.animateOut('left');
            this.reset();
            $wire.swipe('dislike');
        },

        reset() {
            this.currentX = 0;
            this.currentY = 0;
            this.rotation = 0;
            this.opacity = 1;
            this.showLike = false;
            this.showNope = false;
        }
    }"
    @mousemove.window="onDrag($event)"
    @mouseup.window="endDrag()"
    @touchmove.window.prevent="onDrag($event)"
    @touchend.window="endDrag()"
>

    {{-- ── HEADER ── --}}
    <div class="relative z-50 shrink-0 flex items-center justify-between px-4 py-3" style="background-color: #470D4E;">

        {{-- Botón chat (izquierda) --}}
        <a href="{{ route('chats.index') }}" wire:navigate
           class="size-11 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/>
            </svg>
        </a>

        {{-- Logo centro --}}
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/Logo.png') }}" alt="VicioApp" width="48" height="48">
            <span class="text-white font-bold text-2xl tracking-tight">VicioApp</span>
        </div>

        {{-- Botón menú (derecha) — scope Alpine propio para evitar conflicto con drag --}}
        <div class="relative" x-data="{ open: false }">
            <button
                @click.stop="open = !open"
                class="size-11 rounded-full bg-white flex items-center justify-center hover:bg-white/90 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="#49197C">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Overlay oscuro --}}
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
                
                <a    href="{{ route('dashboard') }}"
                    wire:navigate
                    class="w-full text-center px-6 py-4 rounded-2xl font-semibold text-lg whitespace-nowrap shadow-lg transition-colors"
                    style="background: #f5f0eb; color: #49197C;"
                >
                    Mi perfil
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button
                        type="submit"
                        class="w-full text-center px-6 py-4 rounded-2xl font-semibold text-lg whitespace-nowrap shadow-lg transition-colors"
                        style="background: #f5f0eb; color: #49197C;"
                    >
                        Salir ✕
                    </button>
                </form>
            </div>
        </div>
    </div>  

    {{-- ── ZONA DE TARJETA ── --}}
    <div class="relative flex-1 flex items-stretch px-3 pt-3 pb-3 min-h-0">

        @if($noMoreCards)
            {{-- Sin más perfiles --}}
            <div class="flex flex-col items-center justify-center w-full gap-5 text-center px-6">
                <div class="size-24 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center">
                    <span class="text-5xl">👀</span>
                </div>
                <div class="space-y-2">
                    <p class="text-white font-semibold text-xl">Nadie por aquí aún</p>
                    <p class="text-zinc-500 text-sm leading-relaxed">
                        Estás a la espera de que lleguen personas<br>de tu gusto a la fiesta.<br>
                        <span class="text-zinc-600">Vuelve a comprobarlo en unos minutos.</span>
                    </p>
                </div>
                <button wire:click="reload"
                        class="mt-2 px-6 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 text-sm font-medium hover:bg-zinc-700 transition-colors">
                    Actualizar
                </button>
            </div>

        @else

            {{-- Tarjeta de fondo (siguiente) --}}
            @if($nextCard)
                <div class="absolute inset-3 rounded-3xl overflow-hidden pointer-events-none scale-95 opacity-60">
                    <div class="w-full h-full bg-cover bg-center bg-zinc-800"
                         style="background-image: url('{{ $nextCard['profile_photo_url'] }}')">
                    </div>
                </div>
            @endif

            {{-- Tarjeta principal --}}
            @if($currentCard)
                <div
                    class="relative w-full rounded-3xl overflow-hidden cursor-grab active:cursor-grabbing"
                    :style="cardStyle"
                    @mousedown="startDrag($event)"
                    @touchstart="startDrag($event)"
                    wire:key="card-{{ $currentCard['id'] }}"
                >
                    {{-- Foto de fondo --}}
                    <div class="absolute inset-0 bg-cover bg-center bg-zinc-800"
                         style="background-image: url('{{ $currentCard['profile_photo_url'] }}')">
                    </div>

                    {{-- Gradiente overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/10 to-transparent"></div>

                    {{-- Badge LIKE --}}
                    <div
                        x-show="showLike"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute top-8 left-6 border-4 border-green-400 rounded-xl px-4 py-2 rotate-[-15deg]"
                        style="display:none;"
                    >
                        <span class="text-green-400 font-black text-2xl tracking-widest">LIKE</span>
                    </div>

                    {{-- Badge NOPE --}}
                    <div
                        x-show="showNope"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute top-8 right-6 border-4 border-red-400 rounded-xl px-4 py-2 rotate-[15deg]"
                        style="display:none;"
                    >
                        <span class="text-red-400 font-black text-2xl tracking-widest">NOPE</span>
                    </div>

                    {{-- Info usuario (abajo de la tarjeta) --}}
                    <div class="absolute bottom-0 left-0 right-0 px-6 pb-6 pt-16">
                        <h2 class="text-white font-bold text-3xl leading-tight drop-shadow">
                            {{ $currentCard['username'] }}
                            @if($currentCard['age'])
                                <span class="font-light text-white/90"> {{ $currentCard['age'] }}</span>
                            @endif
                        </h2>
                        @if($currentCard['bio'])
                            <p class="text-white/80 text-sm mt-1 pb-4 leading-snug line-clamp-2 drop-shadow">
                                {{ $currentCard['bio'] }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif

        @endif
    </div>

    {{-- ── BOTONES DE ACCIÓN ── --}}
    @if(!$noMoreCards && $currentCard)
        <div class="shrink-0 flex items-center justify-between px-10 pb-8 pt-2">

            {{-- Dislike --}}
            <button
                @click="triggerDislike()"
                class="size-20 rounded-full flex items-center justify-center shadow-xl active:scale-95 transition-transform"
                style="border: 3px solid #ef4444; background: transparent;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-9 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Like --}}
            <button
                @click="triggerLike()"
                class="size-20 rounded-full flex items-center justify-center shadow-xl active:scale-95 transition-transform"
                style="border: 3px solid #10b981; background: transparent;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-9 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </button>

        </div>
    @endif

    {{-- ── MODAL DE MATCH ── --}}
    @if($lastMatch)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
            x-show="showMatch"
            x-transition:enter="transition duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex flex-col items-center gap-6 px-8 text-center">
                <div class="text-6xl animate-bounce">🎉</div>
                <div class="space-y-2">
                    <h2 class="text-white font-black text-4xl tracking-tight">¡Match!</h2>
                    <p class="text-zinc-300 text-lg">Tú y <span class="text-vicio-300 font-semibold">{{ $lastMatch['username'] }}</span> os gustáis</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="size-24 rounded-full border-4 border-vicio-400 overflow-hidden">
                        <img src="{{ auth()->user()->profile_photo_url }}" class="w-full h-full object-cover" alt="Tú"/>
                    </div>
                    <div class="text-3xl">❤️</div>
                    <div class="size-24 rounded-full border-4 border-vicio-400 overflow-hidden">
                        <img src="{{ $lastMatch['profile_photo_url'] }}" class="w-full h-full object-cover" alt="{{ $lastMatch['username'] }}"/>
                    </div>
                </div>
                <div class="flex flex-col gap-3 w-full max-w-xs">
                    <button
                        wire:click="dismissMatch"
                        class="w-full vicio-gradient text-white font-bold py-4 rounded-2xl hover:opacity-90 transition-opacity">
                        💬 Enviar mensaje
                    </button>
                    <button
                        wire:click="dismissMatch"
                        class="w-full bg-zinc-800 text-zinc-300 font-semibold py-3.5 rounded-2xl hover:bg-zinc-700 transition-colors">
                        Seguir viendo perfiles
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
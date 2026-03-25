<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="overflow-hidden" style="background-color: #A678C8;">
<div class="w-full max-w-[430px] mx-auto flex flex-col overflow-y-auto" style="height: 100dvh; background-color: #A678C8;">

    {{-- Header --}}
    <div class="shrink-0 flex items-center gap-3 px-4 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
        <div class="size-8 rounded-full flex items-center justify-center" style="background-color: rgba(255,255,255,0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4 fill-white">
                <path d="M12 2C9.5 5 8 7.5 8 10c0 2.2 1.8 4 4 4s4-1.8 4-4c0-.5-.1-1-.2-1.4C17.2 10 18 11.9 18 14c0 3.3-2.7 6-6 6s-6-2.7-6-6c0-4 3-8 6-10zm0 10c-.6 0-1-.4-1-1 0-.9.5-1.8 1-2.5.5.7 1 1.6 1 2.5 0 .6-.4 1-1 1z"/>
            </svg>
        </div>
        <div>
            <span class="text-white font-bold text-xl leading-tight block">VicioApp</span>
            <span class="text-xs" style="color: rgba(255,255,255,0.6);">Las fiestas de la noche</span>
        </div>

        {{-- Botón logout --}}
        <form method="POST" action="{{ route('logout') }}" class="ml-auto">
            @csrf
            <button type="submit"
                class="size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
                style="background-color: rgba(255,255,255,0.2);"
                title="Cerrar sesión">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>

    {{-- Contenido --}}
    <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">

        {{-- ── FIESTA ACTIVA ── --}}
        @if($active)
            <div style="margin-top: 10px">
                <div class="flex items-center gap-2 mb-3">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.55);">Ahora mismo</p>
                    <span class="flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-white" style="background-color: #2d0a3e;">
                        <span class="size-1.5 rounded-full bg-green-400 animate-pulse inline-block"></span>
                        LIVE
                    </span>
                </div>

                <div class="rounded-2xl overflow-hidden" style="background-color: #2d0a3e;margin-top: 10px">
                    @if($active->cover_image)
                        <div class="h-36 overflow-hidden relative">
                            <img src="{{ asset('storage/' . $active->cover_image) }}"
                                class="w-full h-full object-cover opacity-70" alt="{{ $active->name }}">
                            <div class="absolute inset-0" style="background: linear-gradient(to top, #2d0a3e 10%, transparent 60%);"></div>
                        </div>
                    @endif
                    <div class="px-4 py-4 {{ $active->cover_image ? '-mt-8 relative' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <h2 class="text-white font-bold text-lg leading-tight truncate">{{ $active->name }}</h2>
                                @if($active->location)
                                    <p class="text-xs mt-1 flex items-center gap-1" style="color: rgba(255,255,255,0.6);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $active->location }}
                                    </p>
                                @endif
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">
                                    {{ $active->users()->count() }} personas dentro
                                </p>
                            </div>
                            <span class="flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold text-white shrink-0" style="background-color: rgba(74,222,128,0.2); color: #4ade80;">
                                <span class="size-1.5 rounded-full bg-green-400 animate-pulse inline-block"></span>
                                Activa
                            </span>
                        </div>

                        @if($active->description)
                            <p class="text-xs mt-3 leading-relaxed" style="color: rgba(255,255,255,0.6);">{{ Str::limit($active->description, 100) }}</p>
                        @endif

                        {{-- CTA: unirse --}}
                        <div class="mt-4">
                            @auth
                                @php $alreadyIn = auth()->user()->parties()->where('party_id', $active->id)->exists(); @endphp
                                @if($alreadyIn)
                                    <a href="{{ route('party.register', $active->qr_code) }}"
                                       class="w-full flex items-center justify-center gap-2 py-3 rounded-xl font-semibold text-sm text-white transition-opacity hover:opacity-90"
                                       style="background-color: #49197C;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Volver a la fiesta
                                    </a>
                                @else
                                    <button onclick="openQrScanner()"
                                        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl font-semibold text-sm text-white transition-opacity hover:opacity-90"
                                        style="background-color: #49197C;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        Escanear QR para entrar
                                    </button>
                                @endif
                            @else
                                <button onclick="openQrScanner()"
                                    class="w-full flex items-center justify-center gap-2 py-3 rounded-xl font-semibold text-sm text-white transition-opacity hover:opacity-90"
                                    style="background-color: #49197C;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    Escanear QR para entrar
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── PRÓXIMAS ── --}}
        @if($upcoming->isNotEmpty())
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: rgba(255,255,255,0.55);">Próximas</p>
                <div class="space-y-3" style="margin-top: 10px">
                    @foreach($upcoming as $party)
                        @php $secondsLeft = max(0, now()->diffInSeconds($party->starts_at, false)); @endphp
                        <div class="rounded-2xl px-4 py-4" style="background-color: rgba(255,255,255,0.15);"
                            x-data="{
                                seconds: {{ $secondsLeft }},
                                get days()    { return Math.floor(this.seconds / 86400) },
                                get hours()   { return Math.floor((this.seconds % 86400) / 3600) },
                                get minutes() { return Math.floor((this.seconds % 3600) / 60) },
                                get secs()    { return Math.floor(this.seconds % 60) },
                                get pad()     { return v => String(v).padStart(2,'0') },
                                init() {
                                    if (this.seconds > 0) {
                                        const t = setInterval(() => {
                                            if (this.seconds > 0) this.seconds--;
                                            else clearInterval(t);
                                        }, 1000);
                            }
                                }
                            }"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-white font-semibold text-base truncate">{{ $party->name }}</h3>
                                    @if($party->location)
                                        <p class="text-xs mt-0.5 flex items-center gap-1" style="color: rgba(255,255,255,0.6);">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $party->location }}
                                        </p>
                                    @endif
                                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">
                                        {{ $party->starts_at->format('d M · H:i') }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold shrink-0" style="background-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.85);">
                                    Próxima
                                </span>
                            </div>

                            {{-- Cuenta atrás --}}
                            <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.1);">
                                <p class="text-xs text-center mb-2" style="color: rgba(255,255,255,0.45);">Empieza en</p>
                                <div class="flex gap-2" x-show="seconds > 0">
                                    <div class="flex-1 flex flex-col items-center rounded-xl py-2" style="background-color: rgba(0,0,0,0.15);">
                                        <span class="text-white font-bold text-xl leading-none" x-text="pad(days)">00</span>
                                        <span class="text-xs mt-1" style="color: rgba(255,255,255,0.45);">días</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center rounded-xl py-2" style="background-color: rgba(0,0,0,0.15);">
                                        <span class="text-white font-bold text-xl leading-none" x-text="pad(hours)">00</span>
                                        <span class="text-xs mt-1" style="color: rgba(255,255,255,0.45);">horas</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center rounded-xl py-2" style="background-color: rgba(0,0,0,0.15);">
                                        <span class="text-white font-bold text-xl leading-none" x-text="pad(minutes)">00</span>
                                        <span class="text-xs mt-1" style="color: rgba(255,255,255,0.45);">min</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center rounded-xl py-2" style="background-color: rgba(0,0,0,0.15);">
                                        <span class="text-white font-bold text-xl leading-none" x-text="pad(secs)">00</span>
                                        <span class="text-xs mt-1" style="color: rgba(255,255,255,0.45);">seg</span>
                                    </div>
                                </div>
                                <div x-show="seconds === 0"
                                    class="flex items-center justify-center gap-2 py-2 rounded-xl font-semibold text-sm"
                                    style="background-color: rgba(74,222,128,0.15); color: #4ade80;">
                                    <span class="size-2 rounded-full bg-green-400 animate-pulse inline-block"></span>
                                    ¡Empezando ahora!
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── PASADAS ── --}}
        @if($finished->isNotEmpty())
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: rgba(255,255,255,0.55);">Pasadas</p>
                <div class="space-y-3" style="margin-top: 10px">
                    @foreach($finished as $party)
                        <div class="rounded-2xl px-4 py-4" style="background-color: rgba(0,0,0,0.15);">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-base truncate" style="color: rgba(255,255,255,0.6);">{{ $party->name }}</h3>
                                    @if($party->location)
                                        <p class="text-xs mt-0.5 flex items-center gap-1" style="color: rgba(255,255,255,0.4);">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $party->location }}
                                        </p>
                                    @endif
                                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.35);">
                                        {{ $party->starts_at->format('d M Y') }} · {{ $party->users()->count() }} asistentes
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold shrink-0" style="background-color: rgba(0,0,0,0.2); color: rgba(255,255,255,0.4);">
                                    Finalizada
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Estado vacío total --}}
        @if(!$active && $upcoming->isEmpty() && $finished->isEmpty())
            <div class="flex flex-col items-center justify-center py-32 text-center gap-4 px-6">
                <div class="text-5xl">🎉</div>
                <p class="font-semibold text-white text-base">Aún no hay fiestas</p>
                <p class="text-sm" style="color: rgba(255,255,255,0.6);">Pronto habrá noches épicas por aquí</p>
            </div>
        @endif

    </div>

</div>

@auth
    <livewire:auth.ban-watcher />
@endauth

{{-- ── MODAL ESCÁNER QR ── --}}
<div id="qr-modal" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-black/90 backdrop-blur-sm" style="display:none!important;">
    <div class="w-full max-w-[360px] mx-4 rounded-3xl overflow-hidden" style="background-color: #1a0a2e;">

        {{-- Header modal --}}
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h2 class="text-white font-bold text-base">Escanear QR</h2>
            <button onclick="closeQrScanner()" class="size-8 rounded-full flex items-center justify-center" style="background-color: rgba(255,255,255,0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Visor cámara --}}
        <div class="relative" style="aspect-ratio: 1;">
            <video id="qr-video" class="w-full h-full object-cover" playsinline autoplay muted></video>
            <canvas id="qr-canvas" class="hidden"></canvas>

            {{-- Overlay con marco --}}
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="relative" style="width: 200px; height: 200px;">
                    {{-- Esquinas del marco --}}
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-white rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-white rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-white rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-white rounded-br-lg"></div>
                    {{-- Línea de escaneo animada --}}
                    <div id="qr-scanline" class="absolute left-2 right-2" style="height: 2px; background: #49197C; box-shadow: 0 0 8px #49197C; top: 0; animation: scanline 2s linear infinite;"></div>
                </div>
            </div>

            {{-- Mensaje de estado --}}
            <div class="absolute bottom-0 left-0 right-0 px-4 py-3 text-center" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                <p id="qr-status" class="text-white text-sm font-medium">Apunta al código QR de la fiesta</p>
            </div>
        </div>

        {{-- Footer modal --}}
        <div class="px-5 py-4 text-center">
            <p class="text-xs" style="color: rgba(255,255,255,0.4);">El QR lo tiene el organizador de la fiesta</p>
        </div>
    </div>
</div>

<style>
    @keyframes scanline {
        0%   { top: 0; }
        50%  { top: calc(100% - 2px); }
        100% { top: 0; }
    }
    #qr-modal.active { display: flex !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    let qrStream = null;
    let qrAnimFrame = null;

    function openQrScanner() {
        document.getElementById('qr-modal').classList.add('active');
        startCamera();
    }

    function closeQrScanner() {
        document.getElementById('qr-modal').classList.remove('active');
        stopCamera();
    }

    async function startCamera() {
        const video = document.getElementById('qr-video');
        document.getElementById('qr-status').textContent = 'Apunta al código QR de la fiesta';
        try {
            qrStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            });
            video.srcObject = qrStream;
            video.play();
            video.addEventListener('loadedmetadata', scanFrame);
        } catch (err) {
            document.getElementById('qr-status').textContent = 'No se pudo acceder a la cámara';
        }
    }

    function stopCamera() {
        if (qrStream) {
            qrStream.getTracks().forEach(t => t.stop());
            qrStream = null;
        }
        if (qrAnimFrame) {
            cancelAnimationFrame(qrAnimFrame);
            qrAnimFrame = null;
        }
    }

    function scanFrame() {
        const video  = document.getElementById('qr-video');
        const canvas = document.getElementById('qr-canvas');
        if (video.readyState !== video.HAVE_ENOUGH_DATA) {
            qrAnimFrame = requestAnimationFrame(scanFrame);
            return;
        }
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'dontInvert'
        });
        if (code && code.data) {
            document.getElementById('qr-status').textContent = '✓ QR detectado, redirigiendo...';
            stopCamera();
            window.location.href = code.data;
            return;
        }
        qrAnimFrame = requestAnimationFrame(scanFrame);
    }
</script>

@fluxScripts
</body>
</html>
<?php
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Escanear fiesta')] class extends Component
{
    public function render()
    {
        return view('pages.scan');
    }
}; ?>

<x-layouts::app.sidebar>
<div
    class="h-full flex flex-col bg-zinc-950"
    x-data="{
        scanning: false,
        error: null,
        stream: null,

        async startScan() {
            this.error = null;
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                this.$refs.video.srcObject = this.stream;
                await this.$refs.video.play();
                this.scanning = true;
                this.tick();
            } catch (e) {
                this.error = 'No se pudo acceder a la cámara. Comprueba los permisos.';
            }
        },

        stopScan() {
            this.scanning = false;
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
        },

        tick() {
            if (!this.scanning) return;

            const video = this.$refs.video;
            const canvas = this.$refs.canvas;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'dontInvert',
                });

                if (code) {
                    this.stopScan();
                    this.handleResult(code.data);
                    return;
                }
            }

            requestAnimationFrame(() => this.tick());
        },

        handleResult(url) {
            // Verificar que la URL pertenece a esta app
            const base = window.location.origin + '/party/';
            if (url.startsWith(base)) {
                window.location.href = url;
            } else {
                this.error = 'El QR escaneado no corresponde a una fiesta de VicioApp.';
                this.scanning = false;
            }
        }
    }"
    x-init="startScan()"
    x-on:beforeunload.window="stopScan()"
>
    {{-- Header --}}
    <div class="shrink-0 bg-zinc-950/95 backdrop-blur-sm border-b border-zinc-800 px-4 py-3">
        <div class="max-w-lg mx-auto flex items-center gap-3">
            <a href="{{ route('dashboard') }}" wire:navigate
               class="size-9 rounded-full bg-zinc-800 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-white font-semibold">Escanear fiesta</p>
                <p class="text-zinc-500 text-xs">Apunta al QR de la fiesta</p>
            </div>
        </div>
    </div>

    {{-- Área de cámara --}}
    <div class="flex-1 relative overflow-hidden flex flex-col items-center justify-center">

        {{-- Vídeo de la cámara --}}
        <video
            x-ref="video"
            x-show="scanning"
            playsinline
            muted
            class="absolute inset-0 w-full h-full object-cover"
        ></video>

        {{-- Canvas oculto para procesar frames --}}
        <canvas x-ref="canvas" class="hidden"></canvas>

        {{-- Overlay con marco de escaneo --}}
        <div x-show="scanning" class="absolute inset-0 flex items-center justify-center pointer-events-none">
            {{-- Fondo oscuro alrededor del marco --}}
            <div class="absolute inset-0 bg-black/50"></div>

            {{-- Marco de escaneo --}}
            <div class="relative size-64 z-10">
                {{-- Esquinas del marco --}}
                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-vicio-400 rounded-tl-lg"></div>
                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-vicio-400 rounded-tr-lg"></div>
                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-vicio-400 rounded-bl-lg"></div>
                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-vicio-400 rounded-br-lg"></div>

                {{-- Línea de escaneo animada --}}
                <div class="absolute left-2 right-2 h-0.5 bg-vicio-400 rounded-full shadow-lg shadow-vicio-400/50
                            animate-[scan_2s_ease-in-out_infinite]" style="top: 50%"></div>
            </div>

            {{-- Texto de instrucción --}}
            <p class="absolute bottom-24 text-white text-sm font-medium text-center px-8 z-10
                       drop-shadow-lg">
                Centra el QR dentro del marco
            </p>
        </div>

        {{-- Estado: error --}}
        <div x-show="error" x-cloak class="absolute inset-0 flex flex-col items-center justify-center px-8 gap-6 bg-zinc-950">
            <div class="size-16 rounded-full bg-red-500/20 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="text-center">
                <p class="text-white font-semibold mb-1">Ups</p>
                <p class="text-zinc-400 text-sm" x-text="error"></p>
            </div>
            <button
                @click="error = null; startScan()"
                class="vicio-gradient text-white font-semibold px-8 py-3 rounded-2xl hover:opacity-90 transition-opacity"
            >
                Intentar de nuevo
            </button>
        </div>

        {{-- Estado: cargando cámara --}}
        <div x-show="!scanning && !error" x-cloak class="flex flex-col items-center gap-4">
            <div class="size-12 rounded-full border-4 border-zinc-700 border-t-vicio-400 animate-spin"></div>
            <p class="text-zinc-400 text-sm">Iniciando cámara...</p>
        </div>

    </div>

</div>

{{-- jsQR — librería de decodificación QR sin dependencias --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

{{-- Animación de la línea de escaneo --}}
<style>
    @keyframes scan {
        0%, 100% { transform: translateY(-120px); opacity: 0.8; }
        50%       { transform: translateY(120px);  opacity: 1;   }
    }
</style>
</x-layouts::app.sidebar>
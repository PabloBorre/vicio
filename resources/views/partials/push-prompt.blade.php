@auth
<div
    x-data="{
        show: false,
        loading: false,
        init() {
            if (typeof window.VicioPush === 'undefined') return;
            if (!window.VicioPush.isSupported()) return;
            if (window.VicioPush.getPermission() !== 'default') return;
            if (localStorage.getItem('push_dismissed')) return;
            setTimeout(() => this.show = true, 2000);
        },
        async accept() {
            this.loading = true;
            await window.VicioPush.requestAndSubscribe();
            this.show = false;
            this.loading = false;
        },
        dismiss() {
            this.show = false;
            localStorage.setItem('push_dismissed', '1');
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    x-cloak
    class="fixed bottom-4 left-4 right-4 z-[9998] max-w-sm mx-auto"
>
    <div class="bg-zinc-900 border border-zinc-700 rounded-2xl p-4 shadow-2xl shadow-black/50">
        <div class="flex items-start gap-3">
            <div class="size-10 rounded-full vicio-gradient flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold">Activa las notificaciones</p>
                <p class="text-zinc-400 text-xs mt-0.5">Recibe avisos cuando te escriban, aunque no tengas la app abierta.</p>
                <div class="flex items-center gap-2 mt-3">
                    <button
                        @click="accept()"
                        :disabled="loading"
                        class="px-4 py-1.5 rounded-full vicio-gradient text-white text-xs font-semibold hover:opacity-90 transition-opacity disabled:opacity-50"
                    >
                        <span x-show="!loading">Activar</span>
                        <span x-show="loading">...</span>
                    </button>
                    <button
                        @click="dismiss()"
                        class="px-4 py-1.5 rounded-full text-zinc-400 text-xs hover:text-zinc-200 transition-colors"
                    >
                        Ahora no
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth

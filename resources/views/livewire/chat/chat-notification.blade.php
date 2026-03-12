<div
    x-data="{
        toasts: [],
        add(e) {
            const id = Date.now();
            this.toasts.push({ id, ...e.detail });
            setTimeout(() => this.remove(id), 5000);

            // Notificación nativa del navegador (si la pestaña está en segundo plano)
            if (document.hidden && Notification.permission === 'granted') {
                new Notification(e.detail.username, {
                    body: e.detail.body,
                    icon: e.detail.avatar,
                    tag: 'chat-' + e.detail.matchId,
                });
            }
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-on:show-chat-toast.window="add($event)"
    class="fixed top-0 left-0 right-0 z-[9999] flex flex-col items-center gap-2 pointer-events-none pt-3 px-3"
>
    <template x-for="toast in toasts" :key="toast.id">
        <a
            :href="'/chats/' + toast.matchId"
            class="pointer-events-auto w-full max-w-[92vw] flex items-center gap-3 bg-zinc-900/95 backdrop-blur-lg border border-zinc-700 rounded-2xl px-4 py-4 shadow-2xl shadow-black/60 cursor-pointer hover:border-vicio-500 transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-full"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-full"
        >
            <img :src="toast.avatar" class="size-12 rounded-full object-cover shrink-0 ring-2 ring-vicio-500/30" />
            <div class="flex-1 min-w-0">
                <p class="text-zinc-400 text-[11px] font-medium">Nuevo mensaje</p>
                <p class="text-white text-sm font-bold truncate mt-0.5" x-text="toast.username"></p>
                <p class="text-zinc-300 text-sm truncate mt-0.5" x-text="toast.body"></p>
            </div>
            <button
                @click.prevent.stop="remove(toast.id)"
                class="shrink-0 text-zinc-500 hover:text-zinc-300 transition-colors p-1"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </a>
    </template>
</div>

@script
<script>
// Pedir permisos de notificaciones del navegador
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission().then(permission => {
        console.log('Permiso de notificaciones:', permission);
    });
}

// Registrar service worker y push subscription
if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.register('/sw.js').then(async (registration) => {
        await navigator.serviceWorker.ready;

        let subscription = await registration.pushManager.getSubscription();

        if (!subscription && Notification.permission === 'granted') {
            const vapidKey = document.querySelector('meta[name="vapid-key"]')?.content;
            if (!vapidKey) return;

            const padding = '='.repeat((4 - vapidKey.length % 4) % 4);
            const base64 = (vapidKey + padding).replace(/-/g, '+').replace(/_/g, '/');
            const raw = atob(base64);
            const arr = new Uint8Array(raw.length);
            for (let i = 0; i < raw.length; i++) arr[i] = raw.charCodeAt(i);

            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: arr,
            });
        }

        if (subscription) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrf) return;

            await fetch('/push/subscribe', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(subscription.toJSON()),
            });
        }
    }).catch(err => console.warn('SW registration failed:', err));
}
</script>
@endscript

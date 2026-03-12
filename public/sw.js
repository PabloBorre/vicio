self.addEventListener('push', function (event) {
    if (! event.data) return;

    const data = event.data.json();

    const options = {
        body: data.body || '',
        icon: data.icon || '/favicon.svg',
        badge: '/favicon.svg',
        data: data.data || {},
        vibrate: [200, 100, 200],
        tag: 'chat-' + (data.data?.match_id || 'general'),
        renotify: true,
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'VicioApp', options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data?.url || '/chats';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            // Si ya hay una pestaña abierta, enfocarla y navegar
            for (const client of clientList) {
                if (client.url.includes('/chats') && 'focus' in client) {
                    client.focus();
                    client.navigate(url);
                    return;
                }
            }
            // Si no, abrir nueva pestaña
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

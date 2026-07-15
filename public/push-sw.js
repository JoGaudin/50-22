self.addEventListener('push', (event) => {
    if (!event.data) return;

    const data = event.data.json();

    const title = data.title ?? 'Notification';
    const options = {
        body: data.body ?? '',
        icon: data.icon ?? '/favicon.ico',
        badge: data.badge ?? '/favicon.ico',
        data: data.data ?? {},
        actions: data.actions ?? [],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const url = event.notification.data?.url;

    if (url) {
        event.waitUntil(
            clients
                .matchAll({ type: 'window', includeUncontrolled: true })
                .then((clientList) => {
                    for (const client of clientList) {
                        if (client.url === url && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    if (clients.openWindow) {
                        return clients.openWindow(url);
                    }
                }),
        );
    }
});

self.addEventListener('push', function(event) {
    const data = event.data ? event.data.json() : {};

    const title = data.title || 'Notification';
    const options = {
        body: data.body || 'You have a new notification.',
        icon: data.icon || '/icon.png',
        data: data.data || {},
    };

    // Show native notification
    event.waitUntil(self.registration.showNotification(title, options));

    // Broadcast message to all clients (pages)
    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clients => {
            clients.forEach(client => {
                client.postMessage({
                    type: 'push-notification',
                    title: title,
                    body: options.body,
                    data: options.data,
                });
            });
        })
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const urlToOpen = event.notification.data.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            for (const client of windowClients) {
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

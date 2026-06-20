const CACHE_NAME = 'dpis-v1';

self.addEventListener('install', (e) => {
    self.skipWaiting();
});

self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (e) => {
    // Only handle GET requests, skip POST/PUT/PATCH/DELETE
    if (e.request.method !== 'GET') return;
    // Skip cross-origin requests
    if (!e.request.url.startsWith(self.location.origin)) return;

    e.respondWith(
        fetch(e.request).catch(() =>
            caches.match(e.request).then(cached => cached || new Response('Offline', { status: 503 }))
        )
    );
});

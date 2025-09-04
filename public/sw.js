const CACHE_NAME = 'comida-sm-v1.0.0';
const API_CACHE_NAME = 'comida-sm-api-v1.0.0';

// Files to cache for offline functionality
const STATIC_CACHE_URLS = [
    '/',
    '/js/app.js',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
];

// API endpoints to cache
const API_CACHE_URLS = [
    '/api/customer-by-phone',
    '/api/place-order'
];

// Install event - cache static resources
self.addEventListener('install', event => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        Promise.all([
            caches.open(CACHE_NAME).then(cache => {
                console.log('Service Worker: Caching static files');
                return cache.addAll(STATIC_CACHE_URLS.map(url => new Request(url, {cache: 'reload'})));
            }),
            self.skipWaiting()
        ])
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        Promise.all([
            caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== CACHE_NAME && cacheName !== API_CACHE_NAME) {
                            console.log('Service Worker: Deleting old cache', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            }),
            self.clients.claim()
        ])
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
    const requestUrl = new URL(event.request.url);
    
    // Handle API requests
    if (requestUrl.pathname.startsWith('/api/')) {
        event.respondWith(handleApiRequest(event.request));
        return;
    }
    
    // Handle static resources
    event.respondWith(
        caches.match(event.request).then(response => {
            if (response) {
                return response;
            }
            
            return fetch(event.request).then(response => {
                // Don't cache non-successful responses
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }
                
                // Clone the response
                const responseToCache = response.clone();
                
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, responseToCache);
                });
                
                return response;
            });
        }).catch(() => {
            // Return offline page for navigation requests
            if (event.request.mode === 'navigate') {
                return caches.match('/offline.html');
            }
        })
    );
});

// Handle API requests with network-first strategy
async function handleApiRequest(request) {
    try {
        const response = await fetch(request);
        
        if (response.ok) {
            const cache = await caches.open(API_CACHE_NAME);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        console.log('API request failed, trying cache:', error);
        return caches.match(request);
    }
}

// Push notification event
self.addEventListener('push', event => {
    console.log('Push notification received:', event);
    
    const options = {
        body: 'Seu pedido foi atualizado!',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/badge-72x72.png',
        tag: 'order-update',
        requireInteraction: true,
        actions: [
            {
                action: 'view',
                title: 'Ver Pedido',
                icon: '/icons/action-view.png'
            },
            {
                action: 'dismiss',
                title: 'Dispensar',
                icon: '/icons/action-dismiss.png'
            }
        ],
        data: {
            url: '/'
        }
    };
    
    if (event.data) {
        try {
            const payload = event.data.json();
            options.body = payload.message || options.body;
            options.data.url = payload.url || options.data.url;
            options.tag = payload.tag || options.tag;
        } catch (e) {
            console.log('Failed to parse push payload:', e);
        }
    }
    
    event.waitUntil(
        self.registration.showNotification('Comida SM', options)
    );
});

// Notification click event
self.addEventListener('notificationclick', event => {
    event.notification.close();
    
    if (event.action === 'view') {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    }
});

// Background sync for offline orders
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync-orders') {
        event.waitUntil(syncOfflineOrders());
    }
});

async function syncOfflineOrders() {
    try {
        const cache = await caches.open('offline-orders');
        const requests = await cache.keys();
        
        for (const request of requests) {
            try {
                const response = await fetch(request.clone());
                if (response.ok) {
                    await cache.delete(request);
                }
            } catch (error) {
                console.log('Failed to sync order:', error);
            }
        }
    } catch (error) {
        console.log('Background sync failed:', error);
    }
}

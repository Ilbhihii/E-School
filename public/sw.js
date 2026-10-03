/* ============================================================
   Smart School Academy — Service Worker v1.0.0
   ============================================================ */

const CACHE_NAME = 'ssa-cache-v4';
const STATIC_CACHE = 'ssa-static-v4';
const DYNAMIC_CACHE = 'ssa-dynamic-v4';
const API_CACHE = 'ssa-api-v4';

const PRECACHE_URLS = [
  '/',
  '/css/layouts-3d.css',
  '/css/design-refresh.css',
  '/css/front-refresh.css',
  '/css/auth-refresh.css',
  '/css/light-global.css',
  '/js/global-theme-sync.js',
  '/manifest.json',
  '/images/logoSSA-removebg-preview.png',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-512x512.png',
  '/images/icons/icon-maskable-512x512.png',
  '/images/icons/icon.svg'
];

/* ─── INSTALL — Pre-cache les ressources essentielles ─── */
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        return cache.addAll(PRECACHE_URLS);
      })
      .then(() => {
        return self.skipWaiting();
      })
  );
});

/* ─── ACTIVATE — Nettoie les anciens caches ─── */
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME, STATIC_CACHE, DYNAMIC_CACHE, API_CACHE];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => {
      return self.clients.claim();
    })
  );
});

/* ─── STRATÉGIES DE CACHE ─── */

// Cache First — pour les ressources statiques (CSS, JS, images)
function cacheFirst(request) {
  return caches.match(request)
    .then(cachedResponse => {
      if (cachedResponse) {
        return cachedResponse;
      }
      return fetch(request).then(response => {
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }
        const responseClone = response.clone();
        caches.open(DYNAMIC_CACHE).then(cache => {
          cache.put(request, responseClone);
        });
        return response;
      }).catch(() => {
        // Fallback silencieux si offline
        return new Response(
          JSON.stringify({ offline: true, message: 'Vous êtes hors ligne.' }),
          { status: 503, statusText: 'Service Unavailable', headers: { 'Content-Type': 'application/json' } }
        );
      });
    });
}

// Network First — pour les pages HTML (toujours essayer le réseau d'abord)
function networkFirst(request) {
  return fetch(request)
    .then(response => {
      if (response && response.status === 200) {
        const responseClone = response.clone();
        caches.open(DYNAMIC_CACHE).then(cache => {
          cache.put(request, responseClone);
        });
      }
      return response;
    })
    .catch(() => {
      return caches.match(request).then(cached => {
        if (cached) {
          return cached;
        }
        // Page hors ligne personnalisée
        return caches.match('/');
      });
    });
}

// Network Only — pour les appels API (évite les données périmées)
function networkOnly(request) {
  return fetch(request).catch(() => {
    return new Response(
      JSON.stringify({ offline: true, message: 'Connexion requise pour cette fonctionnalité.' }),
      { status: 503, headers: { 'Content-Type': 'application/json' } }
    );
  });
}

// Stale-While-Revalidate — pour les polices et ressources tierces
function staleWhileRevalidate(request) {
  return caches.match(request).then(cached => {
    const fetchPromise = fetch(request).then(response => {
      if (response && response.status === 200) {
        const responseClone = response.clone();
        caches.open(DYNAMIC_CACHE).then(cache => {
          cache.put(request, responseClone);
        });
      }
      return response;
    }).catch(() => cached);
    return cached || fetchPromise;
  });
}

/* ─── INTERCEPTEUR DE REQUÊTES ─── */
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Ignorer les requêtes non-HTTP(S)
  if (!url.protocol.startsWith('http')) return;

  // Ignorer les requêtes vers des domaines externes (CDN, analytics, etc.)
  if (url.origin !== self.location.origin) {
    event.respondWith(staleWhileRevalidate(request));
    return;
  }

  // Stratégie par type de fichier
  if (request.destination === 'style' ||
      request.destination === 'script' ||
      request.destination === 'font' ||
      request.destination === 'image') {
    event.respondWith(cacheFirst(request));
    return;
  }

  // Les espaces authentifiés et les API ne doivent jamais être mis en cache :
  // cela évite qu'un autre compte sur le même navigateur voie des données privées.
  const protectedPrefixes = [
    '/api/',
    '/admin/',
    '/prof/',
    '/student/',
    '/parent/',
    '/notifications',
  ];

  if (protectedPrefixes.some(prefix => url.pathname.startsWith(prefix))) {
    event.respondWith(networkOnly(request));
    return;
  }

  // Pages HTML publiques — Network First
  if (request.mode === 'navigate') {
    event.respondWith(networkFirst(request));
    return;
  }

  // Par défaut
  event.respondWith(cacheFirst(request));
});

/* ─── GESTION DES MESSAGES (pour le bouton Installer) ─── */
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

/* ─── NOTIFICATIONS PUSH FCM / WEB PUSH ─── */
self.addEventListener('push', event => {
  if (!event.data) return;

  let payload = {};

  try {
    payload = event.data.json() || {};
  } catch (error) {
    payload = { body: event.data.text() };
  }

  const notification = payload.notification || {};
  const data = payload.data || {};
  const fcmOptions = payload.fcmOptions || payload.fcm_options || {};

  const title =
    notification.title
    || data.title
    || payload.title
    || 'Smart School Academy';

  const body =
    notification.body
    || data.body
    || payload.body
    || 'Nouvelle notification';

  const targetUrl =
    data.url
    || fcmOptions.link
    || '/notifications';

  const options = {
    body,
    icon: notification.icon || '/images/icons/icon-192x192.png',
    badge: '/images/icons/icon-192x192.png',
    tag: data.category ? 'ssa-' + data.category : undefined,
    renotify: false,
    vibrate: [180, 80, 180],
    data: {
      url: targetUrl,
      category: data.category || 'general',
    },
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

self.addEventListener('notificationclick', event => {
  event.notification.close();

  const rawUrl = event.notification?.data?.url || '/notifications';
  const destination = new URL(rawUrl, self.location.origin).href;

  event.waitUntil(
    clients.matchAll({
      type: 'window',
      includeUncontrolled: true,
    }).then(windowClients => {
      for (const client of windowClients) {
        try {
          const clientUrl = new URL(client.url);
          const targetUrl = new URL(destination);

          if (clientUrl.origin === targetUrl.origin) {
            if ('navigate' in client) {
              client.navigate(destination);
            }

            if ('focus' in client) {
              return client.focus();
            }
          }
        } catch (error) {
          // Continue vers openWindow.
        }
      }

      return clients.openWindow(destination);
    })
  );
});

// sw.js - Service worker for VX PWA caching
const CACHE_NAME = 'vx-cache-v2';
const ASSETS = [
  './index.php',
  './login.php',
  './manifest.json',
  './uploads/icon-192.png',
  './uploads/icon-512.png'
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (e) => {
  // 1. Only intercept GET requests. Never intercept POST/PUT/DELETE requests (like logins, checkouts, or product saves).
  // 2. Only intercept standard http/https schemes (ignoring browser extensions).
  if (e.request.method !== 'GET' || !e.request.url.startsWith('http')) {
    return; // Pass through to standard browser network fetching
  }

  e.respondWith(
    caches.match(e.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse;
      }
      return fetch(e.request).then((networkResponse) => {
        return networkResponse;
      }).catch((err) => {
        console.error('PWA Fetch Intercept Failed for:', e.request.url, err);
        // Fallback for HTML page navigation requests when offline
        if (e.request.mode === 'navigate') {
          return caches.match('./index.php') || caches.match('./login.php');
        }
      });
    })
  );
});

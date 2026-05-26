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
  e.respondWith(
    caches.match(e.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse;
      }
      return fetch(e.request).catch(() => {
        // Fallback for navigation requests when offline
        if (e.request.mode === 'navigate') {
          return caches.match('./index.php');
        }
      });
    })
  );
});

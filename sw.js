// sw.js - Service worker for VX PWA caching
const CACHE_NAME = 'vx-cache-v1';
const ASSETS = [
  './index.php',
  './login.php',
  './manifest.json'
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS);
    })
  );
});

self.addEventListener('fetch', (e) => {
  // Let network requests pass through normally
  // Feel free to implement offline-cache strategies here
});

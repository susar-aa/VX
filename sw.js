// sw.js - Service worker for VX PWA caching
const CACHE_NAME = 'vx-cache-v3';
const ASSETS = [
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
  // 1. Only intercept GET requests for standard http/https schemes
  if (e.request.method !== 'GET' || !e.request.url.startsWith('http')) {
    return;
  }

  // 2. Network-First: Try live network first. This preserves real-time PHP sessions & redirects.
  e.respondWith(
    fetch(e.request)
      .then((response) => {
        return response;
      })
      .catch((err) => {
        console.warn('Network request failed, trying offline cache for:', e.request.url, err);
        return caches.match(e.request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          // If offline and navigating, return a theme-matching offline page
          if (e.request.mode === 'navigate') {
            return new Response(
              `<!DOCTYPE html>
              <html lang="en">
              <head>
                  <meta charset="UTF-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <title>VX - Offline Mode</title>
                  <style>
                      body { background-color: #030303; color: #fff; font-family: sans-serif; text-align: center; padding: 100px 20px; margin: 0; }
                      h1 { color: #ccff00; font-size: 3rem; margin: 0 0 10px 0; font-weight: 800; tracking-tighter; }
                      p { color: #888; font-size: 0.9rem; font-weight: 500; }
                  </style>
              </head>
              <body>
                  <h1>V<span style="color:#fff;">X</span></h1>
                  <p>You are currently offline. Please check your network and try again.</p>
              </body>
              </html>`,
              {
                headers: { 'Content-Type': 'text/html' }
              }
            );
          }
        });
      })
  );
});

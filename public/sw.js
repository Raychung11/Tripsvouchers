// SLV Tour Guide — minimal service worker
// Caches the app shell + icons so the app installs and launches offline.
// Network-first for HTML so freshly deployed content wins; cache-first for assets.

const CACHE = 'slv-tour-v1';
const SHELL = [
  '/',
  '/chat',
  '/campaigns',
  '/offline',
  '/assets/css/app.css',
  '/assets/img/icon-192.png',
  '/assets/img/icon-512.png',
  '/manifest.webmanifest',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE).then((c) => c.addAll(SHELL).catch(() => {}))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  if (req.method !== 'GET') return;

  const url = new URL(req.url);
  if (url.origin !== self.location.origin) return;

  // Don't cache admin, merchant, auth, or API endpoints.
  if (/^\/(admin|merchant|login|logout|chat\/send|billplz|api)(\/|$)/.test(url.pathname)) {
    return;
  }

  const accept = req.headers.get('accept') || '';
  const isHTML = req.mode === 'navigate' || accept.includes('text/html');

  if (isHTML) {
    // Network-first for pages.
    event.respondWith(
      fetch(req)
        .then((res) => {
          const copy = res.clone();
          caches.open(CACHE).then((c) => c.put(req, copy)).catch(() => {});
          return res;
        })
        .catch(() =>
          caches.match(req).then((hit) => hit || caches.match('/offline') || caches.match('/'))
        )
    );
    return;
  }

  // Cache-first for static assets.
  event.respondWith(
    caches.match(req).then((hit) =>
      hit ||
      fetch(req).then((res) => {
        const copy = res.clone();
        caches.open(CACHE).then((c) => c.put(req, copy)).catch(() => {});
        return res;
      })
    )
  );
});

/* Pondok Huda — portal manajemen (admin/owner) service worker.
 * - Navigasi: network-first, fallback ke cache lalu /offline.html.
 * - Aset statis: stale-while-revalidate.
 * - POST/HTML auth sensitif tidak pernah disajikan dari cache.
 */
const VERSION = 'ph-web-v1';
const STATIC_CACHE = VERSION + '-static';
const PAGES_CACHE = VERSION + '-pages';
const PRECACHE = ['/offline.html', '/manifest.webmanifest'];

self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(function (cache) { return cache.addAll(PRECACHE); })
      .then(function () { return self.skipWaiting(); })
  );
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys()
      .then(function (keys) {
        return Promise.all(keys
          .filter(function (key) { return key.indexOf(VERSION) !== 0; })
          .map(function (key) { return caches.delete(key); }));
      })
      .then(function () { return self.clients.claim(); })
  );
});

function isStaticAsset(url) {
  return /\.(?:css|js|png|jpe?g|gif|svg|ico|woff2?|ttf|webp)$/i.test(url.pathname);
}

function isInternalPdf(url) {
  // Kwitansi dan laporan: selalu dari jaringan (berpotensi berubah tiap unduh).
  return /^\/(?:kwitansi|pdf)\//.test(url.pathname);
}

self.addEventListener('fetch', function (event) {
  const request = event.request;
  if (request.method !== 'GET') return;

  let url;
  try { url = new URL(request.url); } catch (e) { return; }
  if (url.origin !== self.location.origin) return;
  if (url.pathname.startsWith('/api') || isInternalPdf(url)) return;

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).then(function (response) {
        const copy = response.clone();
        caches.open(PAGES_CACHE).then(function (cache) { cache.put(request, copy); });
        return response;
      }).catch(function () {
        return caches.match(request)
          .then(function (cached) { return cached || caches.match('/offline.html'); });
      })
    );
    return;
  }

  if (isStaticAsset(url)) {
    event.respondWith(
      caches.match(request).then(function (cached) {
        const network = fetch(request).then(function (response) {
          const copy = response.clone();
          caches.open(STATIC_CACHE).then(function (cache) { cache.put(request, copy); });
          return response;
        }).catch(function () { return cached; });
        return cached || network;
      })
    );
  }
});

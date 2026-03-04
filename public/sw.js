/* FOCO - Service Worker (PWA básica)
   - Cachea assets del build + páginas clave para carga rápida
   - No implementa offline real para acciones (POST), solo mejora arranque
*/
const CACHE_NAME = "foco-cache-v1";

const URLS_TO_CACHE = [
  "/",
  "/hoy",
  "/build/manifest.json",
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(URLS_TO_CACHE)).catch(() => {})
  );
  self.skipWaiting();
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.map((k) => (k === CACHE_NAME ? null : caches.delete(k))))
    )
  );
  self.clients.claim();
});

self.addEventListener("fetch", (event) => {
  const req = event.request;
  const url = new URL(req.url);

  // Solo cache para GET del mismo origen
  if (req.method !== "GET" || url.origin !== self.location.origin) return;

  // Estrategia: cache-first para assets, network-first para páginas
  const isAsset = url.pathname.startsWith("/build/") || url.pathname.startsWith("/icons/");

  if (isAsset) {
    event.respondWith(
      caches.match(req).then((cached) => cached || fetch(req).then((res) => {
        const copy = res.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(req, copy));
        return res;
      }))
    );
  } else {
    event.respondWith(
      fetch(req).then((res) => {
        const copy = res.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(req, copy));
        return res;
      }).catch(() => caches.match(req))
    );
  }
});

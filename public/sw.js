const CACHE = 'kasiva-shell-v9';
const STATIC = ['/offline.html', '/images/kasiva-logo-icon-128.png', '/images/kasiva-logo-full.png'];
const ONLINE_ONLY_PREFIXES = ['/admin/'];

async function preCacheManifest(cache) {
  try {
    const manifestResp = await fetch('/build/manifest.json');
    const manifest = await manifestResp.json();
    const files = Object.values(manifest).filter(f => f).map(f => '/build/' + f.file);
    await cache.addAll(files);
  } catch (e) {
    console.error('[SW] manifest cache failed:', e);
  }
}

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE).then(cache =>
      Promise.all([cache.addAll(STATIC), preCacheManifest(cache)])
    ).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    Promise.all([
      caches.keys().then(keys => Promise.all(
        keys.filter(key => key !== CACHE).map(key => caches.delete(key))
      )),
      self.clients.claim()
    ])
  );
});

self.addEventListener('message', event => {
  if (event.data?.type !== 'CACHE_SHELL') return;
  const url = new URL(event.data.url || '/app/pos', self.location.origin);
  if (url.origin !== self.location.origin || (!url.pathname.startsWith('/app/') && url.pathname !== '/pos/offline')) return;

  event.waitUntil(
    fetch(url, { credentials: 'include' })
      .then(response => response.ok ? caches.open(CACHE).then(cache => cache.put(url, response)) : undefined)
      .then(() => event.ports[0]?.postMessage({ cached: true }))
      .catch(() => event.ports[0]?.postMessage({ cached: false }))
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) return;
  if (ONLINE_ONLY_PREFIXES.some(p => url.pathname.startsWith(p))) return;

  event.respondWith(
    fetch(event.request)
      .then(response => {
        const cacheableAsset = url.pathname.startsWith('/build/');
        const cacheableShell = event.request.mode === 'navigate' && (url.pathname.startsWith('/app/') || url.pathname === '/pos/offline');
        if (response.ok && (cacheableAsset || cacheableShell)) {
          event.waitUntil(caches.open(CACHE).then(cache => cache.put(event.request, response.clone())));
        }
        return response;
      })
      .catch(() => event.request.mode === 'navigate'
        ? caches.match(event.request).then(cached => cached || caches.match('/app/pos')).then(cached => cached || caches.match('/pos/offline')).then(cached => cached || caches.match('/offline.html'))
        : caches.match(event.request)
      )
  );
});

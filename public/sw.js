const CACHE = 'kasiva-shell-v7';
const STATIC = ['/offline.html', '/app/pos', '/images/kasiva-logo-icon.png', '/images/kasiva-logo-full.png'];
const LOCAL_ROUTES = ['/pos', '/history', '/expenses', '/marketing/members', '/app/pos', '/app/history', '/app/expenses', '/app/members', '/pos/offline'];
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

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) return;
  if (ONLINE_ONLY_PREFIXES.some(p => url.pathname.startsWith(p))) return;

  event.respondWith(
    fetch(event.request)
      .then(response => {
        if (response.ok && (event.request.mode === 'navigate' || url.pathname.startsWith('/build/'))) {
          event.waitUntil(
            caches.open(CACHE).then(cache => cache.put(event.request, response.clone()))
          );
        }
        return response;
      })
      .catch(() => {
        if (event.request.mode === 'navigate' && LOCAL_ROUTES.some(path => url.pathname === path || url.pathname.startsWith(path + '/'))) {
          return caches.match('/app/pos').then(r => r || caches.match('/offline.html'));
        }
        return caches.match(event.request);
      })
  );
});

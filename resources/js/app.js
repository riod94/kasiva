import { upsertAll, put, flushPendingOperations } from './offline/repository.js';

(() => {
    const deviceId = () => {
        let id = localStorage.getItem('kasiva_device_id');
        if (!id) {
            id = crypto.randomUUID();
            localStorage.setItem('kasiva_device_id', id);
        }
        return id;
    };

    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    const registerDevice = async () => {
        if (!navigator.onLine) return;
        try {
            await fetch('/api/v1/sync/devices', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ device_id: deviceId(), platform: 'web', device_name: 'Browser Kasir' }),
            });
        } catch {}
    };

    const pullMaster = async () => {
        if (!navigator.onLine) return;
        try {
            await registerDevice();
            const r = await fetch('/api/v1/sync/pull', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ device_id: deviceId(), cursor: localStorage.getItem('kasiva_sync_cursor') || undefined }),
            });
            if (!r.ok) return;
            const data = await r.json();
            await upsertAll('catalog', data.changes?.products || []);
            await upsertAll('members', data.changes?.loyalty_members || []);
            await put('sync_meta', { id: 'master', cursor: data.cursor });
            localStorage.setItem('kasiva_sync_cursor', data.cursor);
        } catch {}
    };

    const flush = async () => {
        if (!navigator.onLine) return;
        await flushPendingOperations();
    };

    // No goOfflineShell() redirect — single local-first UI via /app/pos

    window.addEventListener('online', () => {
        pullMaster();
        flush();
    });

    if ('serviceWorker' in navigator &&
        (location.protocol === 'https:' || ['localhost', '127.0.0.1'].includes(location.hostname))) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }

    window.kasivaOffline = { flush, pullMaster, registerDevice };
})();

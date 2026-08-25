import { upsertAll, put, saveExpense, flushPendingOperations } from './offline/repository.js';
import QRCode from 'qrcode';

window.KasivaQRCode = QRCode;
queueMicrotask(() => document.dispatchEvent(new CustomEvent('kasiva:qrcode-ready')));

const enhanceKasivaDialogs = (root = document) => {
    root.querySelectorAll?.('.fixed.inset-0.z-50').forEach((overlay) => {
        if (overlay.getAttribute('role')) return;
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        const heading = overlay.querySelector('h1,h2,h3');
        if (heading) {
            if (!heading.id) heading.id = `kasiva-dialog-${crypto.randomUUID()}`;
            overlay.setAttribute('aria-labelledby', heading.id);
        } else {
            overlay.setAttribute('aria-label', 'Dialog Kasiva');
        }
    });
};

document.addEventListener('DOMContentLoaded', () => enhanceKasivaDialogs());
new MutationObserver((records) => records.forEach(({ addedNodes }) => addedNodes.forEach((node) => {
    if (node.nodeType === Node.ELEMENT_NODE) enhanceKasivaDialogs(node.matches?.('.fixed.inset-0.z-50') ? node.parentElement : node);
}))).observe(document.documentElement, { childList: true, subtree: true });

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
        if (!navigator.onLine) return 0;
        return flushPendingOperations();
    };

    const renderConnectionStatus = (online, announce = false) => {
        const badge = document.getElementById('kasiva-network-badge');
        if (badge) {
            badge.className = `hidden min-h-11 items-center gap-2 rounded-xl border px-3 text-xs font-bold sm:flex ${online ? 'border-emerald-500/25 bg-emerald-500/10 text-emerald-500' : 'border-amber-500/25 bg-amber-500/10 text-amber-500'}`;
            badge.innerHTML = `<span class="h-2 w-2 rounded-full ${online ? 'bg-emerald-500' : 'bg-amber-500'}"></span><span>${online ? 'Online' : 'Offline'}</span>`;
        }
        const toast = document.getElementById('kasiva-connection-status');
        if (toast && announce) {
            toast.textContent = online ? 'Koneksi kembali. Sinkronisasi dilanjutkan.' : 'Offline. Transaksi akan disimpan di perangkat.';
            toast.className = `fixed bottom-[calc(5.5rem+env(safe-area-inset-bottom))] left-4 z-[60] rounded-xl px-3 py-2 text-xs font-bold text-white shadow-lg lg:bottom-4 ${online ? 'bg-emerald-600' : 'bg-amber-600'}`;
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }
    };

    const setConnectionStatus = (online = navigator.onLine, announce = false) => {
        renderConnectionStatus(online, announce);
        window.dispatchEvent(new CustomEvent('kasiva-connection-changed', { detail: { online } }));
    };

    // No goOfflineShell() redirect — single local-first UI via /app/pos

    window.addEventListener('online', () => {
        setConnectionStatus(true, true);
        pullMaster().catch(() => {});
        flush().catch(() => {});
    });
    window.addEventListener('offline', () => setConnectionStatus(false, true));
    setConnectionStatus();

    if ('serviceWorker' in navigator &&
        (location.protocol === 'https:' || ['localhost', '127.0.0.1'].includes(location.hostname))) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }

    const saveOfflineExpense = async () => {
        const value = (id) => document.getElementById(id)?.value || '';
        const id = crypto.randomUUID();
        return saveExpense({
            id,
            client_expense_id: id,
            title: value('offline-expense-title').trim(),
            amount: Number(value('offline-expense-amount')),
            category: value('offline-expense-category'),
            expense_date: value('offline-expense-date') || new Date().toISOString(),
            notes: value('offline-expense-notes'),
            sync_status: 'PENDING_SYNC',
        });
    };

    window.kasivaOffline = { flush, pullMaster, registerDevice, saveExpense: saveOfflineExpense };
    window.loadKasivaQrScanner = () => import('html5-qrcode').then(({ Html5Qrcode }) => Html5Qrcode);
})();

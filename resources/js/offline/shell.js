// Kasiva Offline POS Shell
// Local-first UI that works without a network connection.
// Imports all persistence logic from repository.js (non-inline module).

import {
    getCatalog,
    getAll,
    searchCatalog,
    saveCarts,
    loadCarts,
    saveTransaction,
    getTransactionHistory,
    saveExpense,
    getExpenses,
    enqueueOperation,
    getPendingOperations,
    flushPendingOperations,
    bootstrapCatalog,
    getDeviceId,
    getCsrfToken,
    isOnline,
    put,
} from './repository.js';

// --- App state ---

let products = [];
let cart = [];
let carts = [];
let activeCartId;
let member = null;
const state = {
    view: 'products', // products | cart | history | expenses | members
    checkoutMethod: null,
    memberId: null,
    memberName: '',
    memberQrCode: '',
    loyaltySnapshot: null,
    syncStatus: 'offline',
    syncConflicts: [],
};

function parseMemberQr(raw) {
    const s = String(raw || '').trim();
    if (!s) return null;
    if (s.includes('/m/KSV-MBR-')) return 'KSV-MBR-' + (s.split('/m/KSV-MBR-')[1] || '').split('/')[0];
    if (s.includes('/m/NGEPOS-MBR-')) return 'NGEPOS-MBR-' + (s.split('/m/NGEPOS-MBR-')[1] || '').split('/')[0];
    if (s.includes('KSV-MBR-')) return 'KSV-MBR-' + (s.split('KSV-MBR-')[1] || '').split(/[\s/?#]+/)[0];
    if (s.includes('NGEPOS-MBR-')) return 'NGEPOS-MBR-' + (s.split('NGEPOS-MBR-')[1] || '').split(/[\s/?#]+/)[0];
    return s;
}

async function lookupMemberByQr(raw) {
    const code = parseMemberQr(raw);
    if (!code) return null;
    let m = await getAll('members').then((rows) => rows.find((x) => x.qr_code === code) || null);
    if (!m) {
        const suffix = code.replace(/^KSV-MBR-/, '').replace(/^NGEPOS-MBR-/, '');
        m = await getAll('members').then((rows) => rows.find((x) => String(x.qr_code || '').endsWith(suffix)) || null);
    }
    if (!m) {
        m = await getAll('members').then((rows) => rows.find((x) => x.phone === code || x.phone === raw) || null);
    }
    return m;
}

async function lookupMemberByPhone(phone) {
    const p = String(phone || '').trim();
    if (!p) return null;
    return getAll('members').then((rows) => rows.find((x) => x.phone === p) || null);
}

// --- DOM cache ---

const el = (id) => document.getElementById(id);

// --- Rendering ---

function renderProducts() {
    const search = el('search')?.value.toLowerCase() || '';
    const filtered = searchCatalog(products, search);
    const container = el('products');
    if (!container) return;
    container.innerHTML = filtered.length
        ? filtered
              .map(
                  (p) =>
                      `<button class="item" data-id="${p.id}">${p.name}<br><span class="muted">Rp ${Number(p.price).toLocaleString('id-ID')}</span></button>`
              )
              .join('')
        : '<div class="empty">Katalog belum tersedia.</div>';
    document.querySelectorAll('.item').forEach((b) => {
        b.onclick = () => {
            const p = products.find((x) => x.id === b.dataset.id);
            if (!p) return;
            const existing = cart.find((x) => x.id === p.id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({ id: p.id, name: p.name, price: Number(p.price), hpp: Number(p.hpp || 0), qty: 1 });
            }
            renderCart();
            persistCarts().catch(() => {});
        };
    });
}

function renderCart() {
    const container = el('cart');
    if (!container) return;
    container.innerHTML = cart.length
        ? cart
              .map(
                  (x) =>
                      `<div class="cart-row"><span>${x.name} × ${x.qty}</span><b>Rp ${(x.price * x.qty).toLocaleString('id-ID')}</b><button data-remove="${x.id}" class="muted">✕</button></div>`
              )
              .join('')
        : '<div class="empty">Cart kosong</div>';
    el('total').textContent = 'Rp ' + cart.reduce((a, x) => a + x.price * x.qty, 0).toLocaleString('id-ID');

    document.querySelectorAll('[data-remove]').forEach((b) => {
        b.onclick = () => {
            cart = cart.filter((x) => x.id !== b.dataset.remove);
            renderCart();
            persistCarts().catch(() => {});
        };
    });
}

async function renderHistory() {
    const rows = (await getTransactionHistory()).sort((a, b) => String(b.created_at || b.id).localeCompare(String(a.created_at || a.id)));
    const container = el('history');
    if (!container) return;
    container.innerHTML = rows.length
        ? rows
              .map((x) => {
                  const st = x.sync_status || 'PENDING_SYNC';
                  const stClass = st === 'SYNCED' ? 'color:#3edAd7' : st === 'CONFLICT' ? 'color:#ff5a5a' : 'color:#ffcc00';
                  const err = x.sync_status === 'CONFLICT' && x.last_error ? `<br><small style="color:#ff5a5a">Konflik: ${x.last_error}</small>` : '';
                  return `<article class="cart-row"><span><b>${x.receipt_number}</b><br><small>${new Date(x.created_at).toLocaleString('id-ID')} · ${x.payment_method} · <span style="${stClass}">${st}</span>${err}</small></span><button data-receipt="${x.id}" class="muted">Rp ${Number(x.total_amount).toLocaleString('id-ID')}</button></article>`;
              })
              .join('')
        : 'Belum ada transaksi lokal';
    document.querySelectorAll('[data-receipt]').forEach((b) => {
        b.onclick = async () => {
            const rows = await getTransactionHistory();
            const x = rows.find((r) => r.id === b.dataset.receipt);
            if (!x) return;
            alert(
                `${x.receipt_number}\n${(x.items || [])
                    .map((i) => `${i.product_name} x${i.quantity} = Rp ${Number(i.subtotal).toLocaleString('id-ID')}`)
                    .join('\n')}\nTotal Rp ${Number(x.total_amount).toLocaleString('id-ID')}\n${x.sync_status || 'PENDING_SYNC'}`
            );
        };
    });
}

async function renderExpenses() {
    const expenses = await getExpenses();
    const container = el('expenses-list');
    if (!container) return;
    container.innerHTML = expenses.length
        ? expenses
              .map((e) => {
                  const st = e.sync_status || 'PENDING_SYNC';
                  const stClass = st === 'SYNCED' ? 'color:#3edAd7' : st === 'CONFLICT' ? 'color:#ff5a5a' : 'color:#ffcc00';
                  return `<div class="cart-row"><span>${e.title} · ${e.category || ''}</span><span><b>Rp ${Number(e.amount).toLocaleString('id-ID')}</b><br><small style="${stClass}">${st}</small></span></div>`;
              })
              .join('')
        : '<div class="empty">Belum ada pengeluaran</div>';

    // Sync status banner (conflicts / pending count)
    const pending = await getPendingOperations().catch(() => []);
    const syncBanner = el('sync-banner');
    if (!syncBanner) return;
    const conflicts = pending.filter((o) => o.status === 'CONFLICT');
    const failed = pending.filter((o) => o.status === 'FAILED');
    if (conflicts.length) {
        syncBanner.innerHTML = `<div style="background:#ff5a5a;color:#fff;padding:10px 14px;border-radius:12px;margin:12px 0;font-weight:700">${conflicts.length} konflik sinkronisasi — tap untuk detail: ${conflicts.map((c)=>c.last_error||c.type).join('; ')}</div>`;
    } else if (failed.length) {
        syncBanner.innerHTML = `<div style="background:#ffcc00;color:#272d48;padding:10px 14px;border-radius:12px;margin:12px 0;font-weight:700">${failed.length} gagal sinkronisasi — akan retry saat online</div>`;
    } else {
        const p = pending.filter((o) => o.status === 'PENDING').length;
        syncBanner.innerHTML = p ? `<div style="background:#505b93;color:#fff;padding:10px 14px;border-radius:12px;margin:12px 0">${p} antrian sinkronisasi</div>` : '';
    }
}

// --- Cart persistence (per-slot member) ---

async function persistCarts() {
    const current = carts.find((x) => x.id === activeCartId);
    if (current) { current.items = cart; current.member_id = state.memberId; current.member_name = state.memberName; current.member_qr = state.memberQrCode; }
    await saveCarts(carts, activeCartId);
}

function switchCart() {
    const c = carts.find((x) => x.id === activeCartId);
    cart = c ? [...c.items] : [];
    state.memberId = c ? (c.member_id || null) : null;
    state.memberName = c ? (c.member_name || '') : '';
    state.memberQrCode = c ? (c.member_qr || '') : '';
    const select = el('cart-select');
    if (select) {
        select.innerHTML = carts.map((x) => `<option value="${x.id}">${x.name}</option>`).join('');
        select.value = activeCartId || '';
    }
    renderCart();
    renderMemberBadge();
}

function saveCartLocal() {
    const current = carts.find((x) => x.id === activeCartId);
    if (current) { current.items = cart; current.member_id = state.memberId; current.member_name = state.memberName; current.member_qr = state.memberQrCode; }
}

// --- Checkout (atomic write) ---

async function checkout(method) {
    if (!cart.length) return;

    // Validate stock before writing
    for (const item of cart) {
        const product = products.find((p) => p.id === item.id);
        if (!product || Number(product.current_stock) < item.qty) {
            alert('Stok lokal tidak mencukupi: ' + item.name);
            return;
        }
    }

    const total = cart.reduce((a, x) => a + x.price * x.qty, 0);
    const transaction = {
        id: crypto.randomUUID(),
        client_transaction_id: crypto.randomUUID(),
        receipt_number: 'KSV-OFF-' + Date.now(),
        payment_method: method,
        total_amount: total,
        total_hpp: cart.reduce((a, x) => a + x.hpp * x.qty, 0),
        paid_amount: total,
        change_amount: 0,
        sync_status: 'PENDING_SYNC',
        payment_confirmed_manually: method === 'QRIS_STATIC',
        cashier_name: 'Kasir Offline',
        loyalty_member_id: state.memberId || null,
        items: cart.map((x) => ({
            product_id: x.id,
            product_name: x.name,
            unit_price: x.price,
            unit_hpp: x.hpp,
            quantity: x.qty,
            subtotal: x.price * x.qty,
        })),
    };

    // Build stock updates
    const stockUpdates = cart.map((item) => {
        const product = products.find((p) => p.id === item.id);
        return {
            productId: product.id,
            newStock: Number(product.current_stock) - item.qty,
        };
    });

    // Atomic write: transaction + items + stock decrement + outbox
    await saveTransaction(transaction, transaction.items, stockUpdates);

    // Update in-memory products
    for (const item of cart) {
        const product = products.find((p) => p.id === item.id);
        if (product) product.current_stock = Number(product.current_stock) - item.qty;
    }

    cart = [];
    // keep member attached for next order? clear per Kasiva spec: keep until user clears
    saveCartLocal();
    persistCarts().catch(() => {});
    renderCart();
    renderProducts();
    await renderHistory();
    await renderExpenses();
    alert('Tersimpan lokal. Akan disinkronkan saat online.');

    // Try to flush if online
    if (isOnline()) {
        flushPendingOperations().catch(() => {});
    }
}

function renderMemberBadge() {
    const badge = el('member-badge');
    if (!badge) return;
    if (!state.memberId) {
        badge.innerHTML = '<span class="muted">Member belum terhubung</span>';
        return;
    }
    badge.innerHTML = `<span style="background:#3edAd7;color:#272d48;padding:4px 10px;border-radius:999px;font-weight:800">${state.memberName || state.memberQrCode}</span> <button id="member-clear" class="muted" style="margin-left:8px">Lepas</button>`;
    el('member-clear')?.addEventListener('click', () => {
        state.memberId = null; state.memberName = ''; state.memberQrCode = ''; state.loyaltySnapshot = null;
        saveCartLocal(); persistCarts().catch(() => {}); renderMemberBadge();
    });
}

async function attachMemberToCart(raw) {
    const m = await lookupMemberByQr(raw) || await lookupMemberByPhone(raw);
    if (!m) { alert('Member tidak ditemukan di cache offline.'); return false; }
    state.memberId = m.id; state.memberName = m.name || ''; state.memberQrCode = m.qr_code || '';
    // snapshot active loyalty program for stamp eligibility (local)
    const snap = await getAll('sync_meta').then((rows) => rows.find((x) => x.id === 'loyalty_snapshot') || null).catch(() => null);
    state.loyaltySnapshot = snap || null;
    saveCartLocal(); await persistCarts().catch(() => {}); renderMemberBadge();
    return true;
}

// --- Event handlers ---

function setupEventHandlers() {
    el('search')?.addEventListener('input', renderProducts);
    el('search')?.addEventListener('keydown', async (e) => {
        if (e.key === 'Enter') {
            const raw = el('search')?.value || '';
            if (raw.startsWith('KSV-MBR-') || raw.startsWith('NGEPOS-MBR-') || raw.includes('/m/')) {
                e.preventDefault(); await attachMemberToCart(raw);
            }
        }
    });
    el('member-qr')?.addEventListener('keydown', async (e) => {
        if (e.key === 'Enter') { e.preventDefault(); const raw = el('member-qr')?.value || ''; await attachMemberToCart(raw); if (el('member-qr')) el('member-qr').value=''; }
    });
    el('member-qr-btn')?.addEventListener('click', async () => { const raw = el('member-qr')?.value || ''; await attachMemberToCart(raw); if (el('member-qr')) el('member-qr').value=''; });
    el('member-phone')?.addEventListener('keydown', async (e) => {
        if (e.key === 'Enter') { e.preventDefault(); const raw = el('member-phone')?.value || ''; const m = await lookupMemberByPhone(raw); if (!m) { alert('Member tidak ditemukan (HP)'); return; } state.memberId=m.id; state.memberName=m.name||''; state.memberQrCode=m.qr_code||''; saveCartLocal(); await persistCarts().catch(()=>{}); renderMemberBadge(); if (el('member-phone')) el('member-phone').value=''; }
    });
    el('member-phone-btn')?.addEventListener('click', async () => { const raw = el('member-phone')?.value || ''; const m = await lookupMemberByPhone(raw); if (!m) { alert('Member tidak ditemukan (HP)'); return; } state.memberId=m.id; state.memberName=m.name||''; state.memberQrCode=m.qr_code||''; saveCartLocal(); await persistCarts().catch(()=>{}); renderMemberBadge(); if (el('member-phone')) el('member-phone').value=''; });
    el('cash')?.addEventListener('click', () => checkout('CASH'));
    el('qris')?.addEventListener('click', () => checkout('QRIS_STATIC'));
    el('cart-select')?.addEventListener('change', (e) => {
        activeCartId = e.target.value;
        switchCart();
    });
    el('new-cart')?.addEventListener('click', () => {
        const id = crypto.randomUUID();
        carts.push({ id, name: 'Cart ' + (carts.length + 1), items: [] });
        activeCartId = id;
        saveCarts(carts, activeCartId);
        switchCart();
    });

    // Expense form
    el('save-expense')?.addEventListener('click', async () => {
        const v = (id) => el(id)?.value || '';
        const expense = {
            id: crypto.randomUUID(),
            client_expense_id: crypto.randomUUID(),
            title: v('offline-expense-title'),
            amount: Number(v('offline-expense-amount')),
            category: v('offline-expense-category'),
            expense_date: v('offline-expense-date') || new Date().toISOString().split('T')[0],
            notes: v('offline-expense-notes'),
            sync_status: 'PENDING_SYNC',
        };
        await saveExpense(expense);
        alert('Pengeluaran tersimpan lokal.');
        // Clear form
        ['offline-expense-title', 'offline-expense-amount', 'offline-expense-category', 'offline-expense-date', 'offline-expense-notes'].forEach((id) => {
            if (el(id)) el(id).value = '';
        });
        await renderExpenses();
    });
}

// --- Connection management ---

function setupConnectionListeners() {
    window.addEventListener('online', () => {
        el('status').textContent = 'Online — sinkronisasi aktif';
        bootstrapCatalog().catch(() => {});
        flushPendingOperations().catch(() => {});
    });
    window.addEventListener('offline', () => {
        el('status').textContent = 'Offline';
    });
    updateStatus();
}

function updateStatus() {
    if (el('status')) {
        el('status').textContent = navigator.onLine ? 'Online — sinkronisasi aktif' : 'Offline';
    }
}

// --- Service worker ---

async function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        try {
            const reg = await navigator.serviceWorker.register('/sw.js');
            if (navigator.serviceWorker.controller && reg.active) {
                await navigator.serviceWorker.ready;
            }
        } catch (e) {
            console.warn('SW registration failed:', e);
        }
    }
}

// --- Initialization ---

async function init() {
    // Load catalog from IndexedDB
    const cached = await getCatalog();
    products = cached;
    renderProducts();

    // Load cart state from IndexedDB (durable persistence)
    const saved = await loadCarts();
    if (saved && saved.items) {
        carts = saved.items;
        activeCartId = saved.activeCartId;
    } else {
        const newId = crypto.randomUUID();
        carts = [{ id: newId, name: 'Cart 1', items: [] }];
        activeCartId = newId;
    }
    switchCart();

    // Render history + expenses
    await renderHistory();
    await renderExpenses();

    // Setup event handlers
    setupEventHandlers();

    // Setup connection listeners
    setupConnectionListeners();

    // Register service worker
    await registerServiceWorker();

    // Bootstrap catalog if online
    if (isOnline()) {
        const result = await bootstrapCatalog();
        if (result.bootstrapped) {
            products = await getCatalog();
            renderProducts();
        }
        // Flush any pending operations
        await flushPendingOperations();
    }
}

// Expose for debugging
window.kasivaShell = {
    renderProducts,
    renderCart,
    renderHistory,
    switchCart,
    persistCarts,
    bootstrapCatalog,
    flushPendingOperations,
};

// Start the app
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

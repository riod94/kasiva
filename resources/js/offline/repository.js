// Kasiva Local-First Repository Module
// IndexedDB-backed repository for offline POS operations.
// Schema versioning ensures safe upgrades from prototype versions.

const DB_NAME = 'kasiva-offline';
const DB_VERSION = 4;
const STORES = [
    'catalog',
    'carts',
    'members',
    'transactions',
    'transaction_items',
    'expenses',
    'pending_operations',
    'sync_meta',
];

// --- Schema versioning / migration ---

function openDb() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, DB_VERSION);
        req.onupgradeneeded = () => {
            const db = req.result;
            STORES.forEach((name) => {
                if (!db.objectStoreNames.contains(name)) {
                    db.createObjectStore(name, { keyPath: 'id' });
                }
            });
            // Migration from v3: add transaction_items store if upgrading
            if (!db.objectStoreNames.contains('transaction_items')) {
                db.createObjectStore('transaction_items', { keyPath: 'id' });
            }
        };
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

// --- Low-level helpers ---

export async function put(store, value) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(store, 'readwrite');
        tx.objectStore(store).put(value);
        tx.oncomplete = () => resolve();
        tx.onerror = () => reject(tx.error);
    });
}

export async function get(store, id) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
        const req = db.transaction(store).objectStore(store).get(id);
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

export async function getAll(store) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
        const req = db.transaction(store).objectStore(store).getAll();
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

export async function remove(store, id) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(store, 'readwrite');
        tx.objectStore(store).delete(id);
        tx.oncomplete = () => resolve();
        tx.onerror = () => reject(tx.error);
    });
}

export async function clear(store) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(store, 'readwrite');
        tx.objectStore(store).clear();
        tx.oncomplete = () => resolve();
        tx.onerror = () => reject(tx.error);
    });
}

export async function upsertAll(store, values) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(store, 'readwrite');
        const os = tx.objectStore(store);
        for (const value of values) {
            os.put({ ...value, id: value.id });
        }
        tx.oncomplete = () => resolve();
        tx.onerror = () => reject(tx.error);
    });
}

export async function replaceAll(store, values) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(store, 'readwrite');
        const os = tx.objectStore(store);
        os.clear();
        for (const value of values) os.put({ ...value, id: value.id });
        tx.oncomplete = () => resolve();
        tx.onerror = () => reject(tx.error);
        tx.onabort = () => reject(tx.error || new Error('replaceAll aborted'));
    });
}

// --- Device / auth helpers ---

export function getDeviceId() {
    let id = localStorage.getItem('kasiva_device_id');
    if (!id) {
        id = crypto.randomUUID();
        localStorage.setItem('kasiva_device_id', id);
    }
    return id;
}

export function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

export function isOnline() {
    return navigator.onLine;
}

// --- CatalogRepository ---

export async function getCatalog() {
    return getAll('catalog');
}

export async function getCatalogBySku(sku) {
    const items = await getAll('catalog');
    return items.find((p) => p.sku === sku) || null;
}

export function searchCatalog(items, search) {
    if (!search) return items;
    const term = search.toLowerCase();
    return items.filter((p) => p.name?.toLowerCase().includes(term) || p.sku?.toLowerCase().includes(term));
}

export async function bootstrapCatalog() {
    if (!isOnline()) return { bootstrapped: false, reason: 'offline' };

    const deviceId = getDeviceId();
    const csrf = getCsrfToken();

    // Register device
    try {
        await fetch('/api/v1/sync/devices', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ device_id: deviceId, platform: 'web', device_name: 'Browser Kasir' }),
        });
    } catch {
        // device registration best-effort
    }

    // Pull catalog + members + loyalty snapshot
    let cursor = localStorage.getItem('kasiva_sync_cursor');
    try {
        const res = await fetch('/api/v1/sync/pull', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ device_id: deviceId, cursor: cursor || undefined }),
        });
        if (!res.ok) {
            return { bootstrapped: false, reason: `HTTP ${res.status}` };
        }
        const data = await res.json();
        const products = data.changes?.products || [];
        const members = data.changes?.loyalty_members || data.changes?.members || [];
        const loyalty = data.changes?.loyalty_programs || data.changes?.loyalty || [];
        await upsertAll('catalog', products);
        await upsertAll('members', members);
        if (Array.isArray(loyalty) && loyalty.length) {
            for (const prog of loyalty) await put('sync_meta', { id: 'loyalty_' + prog.id, ...prog });
            await put('sync_meta', { id: 'loyalty_snapshot', programs: loyalty, at: new Date().toISOString() });
        } else if (Array.isArray(data.loyalty_programs)) {
            await put('sync_meta', { id: 'loyalty_snapshot', programs: data.loyalty_programs, at: new Date().toISOString() });
        }
        if (data.cursor) {
            await put('sync_meta', { id: 'master', cursor: data.cursor });
            localStorage.setItem('kasiva_sync_cursor', data.cursor);
        }
        return { bootstrapped: true, products: products.length, members: members.length };
    } catch {
        return { bootstrapped: false, reason: 'network error' };
    }
}

// --- CartRepository ---

export async function saveCarts(carts, activeCartId) {
    await put('carts', { id: 'carts', items: carts, activeCartId });
}

export async function loadCarts() {
    return get('carts', 'carts');
}

// --- TransactionRepository ---

export async function saveTransaction(transaction, items, stockUpdates) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(['transactions', 'transaction_items', 'catalog', 'pending_operations'], 'readwrite');
        const txStore = tx.objectStore('transactions');
        const itemStore = tx.objectStore('transaction_items');
        const catalogStore = tx.objectStore('catalog');
        const outboxStore = tx.objectStore('pending_operations');

        // Write the transaction
        txStore.put({
            id: transaction.id,
            ...transaction,
            created_at: new Date().toISOString(),
        });

        // Write line items
        for (const item of items) {
            itemStore.put({ id: crypto.randomUUID(), ...item });
        }

        // Decrement stock (for sell operations)
        for (const update of stockUpdates) {
            const product = catalogStore.get(update.productId);
            product.onsuccess = () => {
                const record = product.result;
                if (record) {
                    record.current_stock = update.newStock;
                    catalogStore.put(record);
                }
            };
        }

        // Append outbox operation
        outboxStore.put({
            id: crypto.randomUUID(),
            type: 'transaction',
            payload: transaction,
            status: 'PENDING',
            created_at: new Date().toISOString(),
        });

        tx.oncomplete = () => resolve(transaction.id);
        tx.onerror = () => reject(tx.error);
    });
}

export async function getTransactionHistory() {
    return getAll('transactions');
}

export async function getTransactionById(id) {
    return get('transactions', id);
}

// --- ExpenseRepository ---

export async function saveExpense(expense) {
    const db = await openDb();
    const id = expense.id || crypto.randomUUID();
    const record = { ...expense, id, client_expense_id: expense.client_expense_id || id, created_at: new Date().toISOString() };
    return new Promise((resolve, reject) => {
        const tx = db.transaction(['expenses', 'pending_operations'], 'readwrite');
        tx.objectStore('expenses').put(record);
        tx.objectStore('pending_operations').put({ id: crypto.randomUUID(), type: 'expense', payload: record, status: 'PENDING', created_at: new Date().toISOString() });
        tx.oncomplete = () => resolve(id);
        tx.onerror = () => reject(tx.error);
    });
}

export async function getExpenses() {
    return getAll('expenses');
}

// --- MemberRepository ---

export async function getMemberByQrCode(qrCode) {
    const members = await getAll('members');
    return members.find((m) => m.qr_code === qrCode) || null;
}

export async function getMemberByPhone(phone) {
    const members = await getAll('members');
    return members.find((m) => m.phone === phone) || null;
}

// --- SyncRepository (outbox) ---

export async function enqueueOperation(type, payload) {
    return put('pending_operations', { id: crypto.randomUUID(), type, payload, status: 'PENDING', created_at: new Date().toISOString() });
}

export async function getPendingOperations() {
    return getAll('pending_operations');
}

export async function markOperationSynced(id) {
    const existing = await get('pending_operations', id);
    if (!existing) return;
    return put('pending_operations', { ...existing, id, status: 'SYNCED', last_error: null, synced_at: new Date().toISOString() });
}

export async function markOperationFailed(id, error) {
    const existing = await get('pending_operations', id);
    if (!existing) return;
    return put('pending_operations', { ...existing, id, status: 'FAILED', last_error: error || 'FAILED', attempts: (existing.attempts || 0) + 1 });
}

export async function markOperationConflict(id, error) {
    const existing = await get('pending_operations', id);
    if (!existing) return;
    return put('pending_operations', { ...existing, id, status: 'CONFLICT', last_error: error || 'CONFLICT' });
}

// --- Connection manager ---

const MAX_SYNC_ATTEMPTS = 3;
const OP_MAP = {
    transaction: { operation: 'UPSERT_TRANSACTION', entity_type: 'transaction' },
    expense: { operation: 'UPSERT_EXPENSE', entity_type: 'expense' },
    member: { operation: 'UPSERT_MEMBER', entity_type: 'member' },
    stamp: { operation: 'ADD_LOYALTY_STAMP', entity_type: 'loyalty_stamp' },
    reward: { operation: 'UPSERT_CUSTOMER_REWARD', entity_type: 'customer_reward' },
};

let flushInFlight;

export function flushPendingOperations() {
    if (flushInFlight) return flushInFlight;
    flushInFlight = flushPendingOperationsNow().finally(() => { flushInFlight = null; });
    return flushInFlight;
}

async function flushPendingOperationsNow() {
    if (!isOnline()) return 0;
    const csrf = getCsrfToken();
    const deviceId = getDeviceId();
    const ops = await getPendingOperations();
    const pending = ops.filter((o) => o.status === 'PENDING' || (o.status === 'FAILED' && (o.attempts || 0) < MAX_SYNC_ATTEMPTS));
    if (!pending.length) return 0;

    const operations = pending.map((op) => {
        const mapping = OP_MAP[op.type] || { operation: String(op.type || 'UPSERT_TRANSACTION').toUpperCase(), entity_type: op.type || 'transaction' };
        const payload = op.payload || {};
        const entityId = payload.client_transaction_id || payload.client_expense_id || payload.client_member_id || payload.client_stamp_id || payload.client_reward_id || payload.id || null;
        return { id: op.id, operation: mapping.operation, entity_type: mapping.entity_type, entity_id: entityId, payload };
    });

    try {
        try {
            await fetch('/api/v1/sync/devices', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ device_id: deviceId, platform: 'web', device_name: 'Browser Kasir' }),
            });
        } catch {}
        const res = await fetch('/api/v1/sync/push', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ device_id: deviceId, operations }),
        });
        if (!res.ok) {
            const txt = `HTTP ${res.status}`;
            for (const op of pending) await markOperationFailed(op.id, txt);
            return 0;
        }
        const data = await res.json();
        const results = Array.isArray(data.results) ? data.results : [];
        const resultById = new Map(results.filter((result) => result?.id).map((result) => [result.id, result]));
        let synced = 0;
        for (const op of pending) {
            const result = resultById.get(op.id);
            if (!result) {
                await markOperationFailed(op.id, 'Missing sync result');
                continue;
            }
            const st = result.status;
            if (st === 'SYNCED') { await markOperationSynced(op.id); synced++; }
            else if (st === 'CONFLICT') { await markOperationConflict(op.id, result.last_error || result.error || 'CONFLICT'); }
            else { await markOperationFailed(op.id, result.last_error || result.error || st || 'Unknown sync status'); }
        }
        return synced;
    } catch (error) {
        for (const op of pending) await markOperationFailed(op.id, error?.message || 'Network error');
        return 0;
    }
}

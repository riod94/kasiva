import { test, expect } from '@playwright/test';

test('POS shell reloads and checks out fully offline', async ({ page, context }) => {
  await page.goto('/pos/offline');
  await page.evaluate(async () => {
    const db = await new Promise<IDBDatabase>((resolve, reject) => { const r=indexedDB.open('kasiva-offline',4); r.onupgradeneeded=()=>['catalog','carts','members','transactions','expenses','pending_operations','sync_meta'].forEach(n=>{if(!r.result.objectStoreNames.contains(n))r.result.createObjectStore(n,{keyPath:'id'})}); r.onsuccess=()=>resolve(r.result); r.onerror=()=>reject(r.error); });
    await new Promise<void>((resolve,reject)=>{ const t=db.transaction('catalog','readwrite'); t.objectStore('catalog').put({id:'11111111-1111-4111-8111-111111111111',name:'Kopi Offline',sku:'OFF-1',price:15000,hpp:6000,current_stock:5,is_active:true}); t.oncomplete=()=>resolve(); t.onerror=()=>reject(t.error); });
  });
  await page.evaluate(async () => {
    await navigator.serviceWorker.ready;
    if (!navigator.serviceWorker.controller) await new Promise<void>(resolve => navigator.serviceWorker.addEventListener('controllerchange', () => resolve(), { once: true }));
  });
  await context.setOffline(true);
  await page.reload();
  await expect(page.getByText('Kopi Offline')).toBeVisible();
  for (const route of ['/history', '/expenses', '/marketing/members', '/pos']) {
    await page.goto(route);
    await expect(page.getByText('Kasiva POS')).toBeVisible();
    await expect(page.getByText('Kopi Offline')).toBeVisible();
  }
  await page.getByText('Kopi Offline').click();
  page.once('dialog', dialog => dialog.accept());
  await page.getByRole('button', { name: 'Bayar Tunai' }).click();
  await expect(page.getByText(/KSV-OFF-/)).toBeVisible();
  const state = await page.evaluate(async () => {
    const db = await new Promise<IDBDatabase>((resolve,reject)=>{const r=indexedDB.open('kasiva-offline',4);r.onsuccess=()=>resolve(r.result);r.onerror=()=>reject(r.error)});
    const all=(store:string)=>new Promise<any[]>((resolve,reject)=>{const r=db.transaction(store).objectStore(store).getAll();r.onsuccess=()=>resolve(r.result);r.onerror=()=>reject(r.error)});
    return { transactions:await all('transactions'), pending:await all('pending_operations'), catalog:await all('catalog') };
  });
  expect(state.transactions).toHaveLength(1);
  expect(state.transactions[0].sync_status).toBe('PENDING_SYNC');
  expect(state.pending).toHaveLength(1);
  expect(state.catalog[0].current_stock).toBe(4);
});

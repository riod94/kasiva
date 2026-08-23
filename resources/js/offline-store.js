const DB_NAME='kasiva-offline', DB_VERSION=3;
const STORES=['catalog','carts','members','transactions','expenses','pending_operations','sync_meta'];
export function openOfflineDb(){return new Promise((resolve,reject)=>{const r=indexedDB.open(DB_NAME,DB_VERSION);r.onupgradeneeded=()=>STORES.forEach(n=>{if(!r.result.objectStoreNames.contains(n))r.result.createObjectStore(n,{keyPath:'id'});});r.onsuccess=()=>resolve(r.result);r.onerror=()=>reject(r.error);});}
export async function put(store,value){const db=await openOfflineDb();return new Promise((resolve,reject)=>{const t=db.transaction(store,'readwrite');t.objectStore(store).put(value);t.oncomplete=resolve;t.onerror=()=>reject(t.error);});}
export async function remove(store,id){const db=await openOfflineDb();return new Promise((resolve,reject)=>{const t=db.transaction(store,'readwrite');t.objectStore(store).delete(id);t.oncomplete=resolve;t.onerror=()=>reject(t.error);});}
export async function all(store){const db=await openOfflineDb();return new Promise((resolve,reject)=>{const r=db.transaction(store).objectStore(store).getAll();r.onsuccess=()=>resolve(r.result);r.onerror=()=>reject(r.error);});}
export async function queue(type,payload){return put('pending_operations',{id:crypto.randomUUID(),type,payload,created_at:new Date().toISOString(),status:'PENDING'});}
export async function pending(){return all('pending_operations');}
export async function clear(store){const db=await openOfflineDb();return new Promise((resolve,reject)=>{const t=db.transaction(store,'readwrite');t.objectStore(store).clear();t.oncomplete=resolve;t.onerror=()=>reject(t.error);});}
export async function replaceAll(store, values){await clear(store); for(const value of values) await put(store,{...value,id:value.id});}
export async function upsertAll(store, values){for(const value of values) await put(store,{...value,id:value.id});}

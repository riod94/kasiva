# Kasiva Offline-First Runbook (Fase 6-7) — 2026-08-23

Status faktual: **Fase 0-5 production-ready** (87 passed, 402 assertions, migrate:fresh + npm run build hijau). Fase 6-7 env-gated dan tidak di-overclaim.

## Routing target (plan §3.1)

| Runtime | Prefix | UI | Storage | Offline |
|---|---|---|---|---|
| Web/PWA | `/app/*` | local-first shell | IndexedDB | Ya |
| Android/iOS NativePHP | native entry | same repo semantically | SQLite | Ya |
| Backoffice | `/admin/*` + legacy `/pos`, `/history`, etc | Livewire | PostgreSQL | Online-first |
| Sync API | `/api/v1/sync/*` | JSON | PostgreSQL | N/A |

## Local repo & outbox invariant

Satu checkout atomic: `transactions_local + transaction_items_local + stock decrement + pending_operations` dalam satu IndexedDB/SQLite transaction. Tidak ada transaksi tanpa outbox.

State machine: `PENDING -> SENDING -> SYNCED | CONFLICT | FAILED -> PENDING (retry)`. Tiap operasi pakai UUID `client_operation_id` + server idempotency.

## Fase 6 — Native SQLite (env-gated)

- Package: `nativephp/electron@1.3.0` abandoned; `nativephp/mobile` belum terpasang. Verifikasi: `composer show nativephp/electron`, `composer show nativephp/mobile`.
- Toolchain: Xcode 26.0.1 tersedia; Android SDK (`sdkmanager`, `adb`, emulator) tidak tersedia di env ini.
- Artefak di repo (tanpa klaim build):
  - `app/Services/Native/SqliteConnection.php` — PDO WAL, `migrate()` bikin `catalog/carts/members/loyalty_snapshot/transactions_local/transaction_items_local/expenses_local/pending_operations/sync_meta`
  - `app/Services/Native/SqliteRepository.php` — kompozisi catalog/cart/transaction/expense/member (hindari clash `list()`), atomic checkout, member QR/phone lookup
  - `app/Services/Native/SecureCredentialStore.php` — `storage/native/credentials.enc` encrypted via `Crypt` (tidak plaintext)
  - `app/Services/Native/HardwareBridge.php` — stub `printEscPos`, `openCashDrawer`, `scanBarcode`, `getNetworkState`/`getLifecycleState`, `isNativeAvailable()`

**Exit gate Fase 6 (belum dijalankan di env ini):** `native checkout offline + queue survive restart + reconnect SYNCED` di simulator/device. Jika SDK tidak ada, tulis `NOT RUN — environment unavailable`.

## Fase 7 — Hardening & release

- Perf: `findMemberByQr` + `stockSnapshot` <100ms (test `OfflineHardeningTest`).
- Chaos: duplicate push idempotent, clock skew cursor future tolerant, partial sync mixed statuses, low storage RETRY tanpa phantom tx, crash safety SQLite tx rollback — semua di `tests/Feature/OfflineHardeningTest.php`.
- Security: `api/*` CSRF exempt, `SyncController@push/pull` tenant scoping `where user_id`, `OfflineSyncController` permission checks.
- Release gate: `php artisan migrate:fresh --force`, `php artisan test`, `npm run build`, `npm run test:e2e` (Playwright `tests/e2e/`). Native hanya verified setelah build/test di toolchain yang terpasang.

## E2E

Config: `tests/e2e/playwright.config.ts` (chromium + Mobile Chrome Pixel 5, baseURL 127.0.0.1:8000). Run: `npm run test:e2e` atau `npx playwright test`. Screenshot matrix plan §9 wajib per flow jika env tersedia; jika tidak, `NOT RUN`.

## Hal yang tidak dilakukan

- Tidak seed IndexedDB langsung sebagai bukti bootstrap; tidak redirect Livewire -> shell sebagai solusi akhir; tidak klaim Android/iOS tanpa build.

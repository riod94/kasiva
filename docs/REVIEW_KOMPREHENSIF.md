# Review Komprehensif — Kasiva POS

Laporan review arsitektur, kode, keamanan, dan kualitas proyek **Kasiva POS** (Laravel + Livewire + NativePHP, local-first / offline-first). Dilakukan pada 2026-08-25.

> Sumber: traversal `app/`, `routes/`, `database/migrations`, `resources/js/offline`, `tests/`, `docs/`. CLI PHP tidak tersedia di environment review, sehingga linting dinamis tidak dijalankan — temuan didasarkan pada review statis.

---

## 1. Ringkasan Eksekutif

Kasiva adalah aplikasi POS SaaS multi-platform berbasis **Laravel 12** (dokumen/flyer menukiskan "Laravel 13"). Arsitektur **local-first + NativePHP bridge** memadat. Kode produk utama (CashierScreen, HppCalculator, SyncQueueProcessor) cukup solid untuk alur kasir inti, namun ada **beberapa bug logika, celah keamanan kredensial, dan gap fitur vs dokumen**. Ringkasan:

| Kategori | Rating | Catatan |
|---|---|---|
| Arsitektur | 🟡 Baik | Pola offline-first jelas, tapi dokumen tertinggal |
| Keamanan | 🟠 Perlu perbaikan | PIN plaintext, kredensial default lemah, bypass owner |
| Inti Kasir (checkout) | 🟠 Ada bug | QRIS tanpa konfirmasi, loyalty gift tak mengurangi stok |
| Sync / Offline | 🟢 Lumayan kuat | LockForUpdate dipakai, idempotent, retry/backoff |
| Data/Konsistensi | 🟠 Drift | Method pembayaran online vs offline tidak selaras |
| Dokumentasi | 🟡 Drift | Versi framework, model data, fitur tidak sinkron |
| Tes | 🟢 Ada niat | Pest-style, cukup tersebar, tapi perlu dijalankan |

---

## 2. Arsitektur & Struktur

- **Stack**: Laravel 12 (`composer.json` membutuhkan `laravel/framework ^12.0`), PHP 8.3+, Livewire 4, Tailwind v4, Vite 8, NativePHP Electron, SQLite lokal, PostgreSQL cloud.
  - **Drift dokumen**: `README.md` dan `docs/04_HLD.md` merepresentasikan "Laravel 13". Ini berpotensi membingungkan. → **Samakan versi**.
- **Layer**: Controller API (`SyncController`) → Service (`SyncQueueProcessor`, `OfflineTransactionSync`, `HppCalculatorService`, `LoyaltyService`) → Model → SQLite lokal / PG cloud.
- **Offline-first**: `resources/js/offline/repository.js` (IndexedDB v4), `sw.js` (shell cache + network-first untuk `/build/`, fall back ke `offline.html`). NativePHP bridge (`app/Services/Native/`).
- **Gatekeeper akses**: Defense-in-depth — middleware HTTP `RequirePermission` + pengecekan di Livewire `mount()`/controller. Matriks peran Owner/Manager/Kasir jelas.

Struktur direktori rapi (`Contracts/`, `DTO/`, `Repositories/`, `Services/Native/`) mencerminkan niat desain kontrak repository (`RepositoryContractTest` ada).

---

## 3. Temuan Kritis (Prioritas Tinggi)

### 3.1 PIN terdaftar **plaintext** di alur registrasi
- `app/Livewire/Auth/Register.php:50` → `'pin' => '123456'`. Kolom `pin` di migrasi `string('pin', 6)->nullable()` **bukan** hash.
- Hanya `StaffManager` yang melakukan `Hash::make($this->pin)`; alur **registrasi sendiri tidak**. Artinya PIN default `123456` tersimpan apaga di DB.
- **Dampak**: bocor credential bypass otentikasi PIN keamanan kasir.
- **Rekomendasi**: hash PIN saat registrasi & perbaikan; jadikan kolom `pin` hashed string. Tambahkan cast `hashed` bila pakai. Pertimbangkan apakah PIN perlu ada — biasanya kasir login via akun, bukan PIN.

### 3.2 QRIS dapat ditandai "lunas" tanpa konfirmasi pembayaran
- `CashierScreen::processCheckout()` langkah `QRIS_DISPLAY` memanggil `processCheckout` langsung ketika **tidak ada flag** `payment_confirmed_manually=true` (lihat blade kira-kira 558).
- Lawanya, alur offline/sync (`OfflineTransactionSync`, `Transaction` model) **memaksa** `payment_confirmed_manually` untuk `QRIS_STATIC`.
- **Dampak**: kasir dapat "menyelesaikan" transaksi QRIS yang belum dibayar pelanggan → kerugian.
- **Rekomendasi**: tambahkan konfirmasi kasir ("Sudah dibayar?") untuk QRIS di UI, set `payment_confirmed_manually=true`, dan validasi di service.

### 3.3 Loyalty "gift" product tidak mengurangi stok
- Di `processCheckout`, item keranjang memanggil `deductRecipeStockForCheckout`, tetapi **`$rewardProduct` (produk gratis dari reward loyalitas) tidak pernah mengurangi stok BTO-nya**. Hanya ditambahkan sebagai `TransactionItem` dengan HPP.
- **Dampak**: stok barang gratis tidak berkurang → inventaris tidak real → HPP laporan meleset.
- **Rekomendasi**: panggil `deductRecipeStockForCheckout($rewardProduct, 1)` di dalam `DB::transaction`.

### 3.4 Split payment tidak divalidasi
- `SPLIT_PAYMENT`: `paid = splitCashAmount + splitQrisAmount`, `change = 0`. **Tidak ada validasi** jumlah split sama dengan total. Kasir bisa masukkan angka sembarang → transaksi tercatat salah.
- **Rekomendasi**: validasi `abs(paid - totalAmount) < 0.01` atau paksa `paidAmount = totalAmount` dan hitung kembalikan.

---

## 4. Keamanan & Kredensial

| Item | Temuan | Lokasi |
|---|---|---|
| PIN plaintext | Registrasi tidak hash | `Register.php:50` |
| Kredensial lemah default | `password123` / `123456` di **Production** seeder | `KasivaProductionSeeder.php:117-121` |
| Hash password | Baik (`Hash::make`) | semua seed & register |
| Bypass owner | `isOwner()`: `role?->slug==='owner' OR empty($this->role_id)`. User dengan `role_id` NULL/empty diperlakukan sebagai owner **secara diam-diam**. Jika ada celah data (mis. import user role_id kosong), mereka dapat akses penuh. | `User.php:103-106` |
| RBAC | Solid — middleware `RequirePermission` + 403 eksplisit. Admin-aliases juga dilindungi. | `RequirePermission.php`, `web.php` |
| Rate limiting | **Tidak ada** throttling login/otp. | — |
| CSRF/Session | Standar Laravel (baik). | — |

**Rekomendasi**: hapus kredensial default dari ProductionSeeder (gunakan seeder terpisah *dev-only*), set minimum `role_id` NOT NULL, tambahkan rate limiting login, dan jadikan PIN hashed.

### 4.1 Drift dokumen keamanan
- README menyebutkan "Defense-in-Depth" dan "RBAC Enforced" — ini benar di kode, tapi tidak tersedia rate-limit/brute-force protection, sehingga klaim "RBAC Enforced" melimpau.

---

## 5. Bug Logika & Ingin Konsistensi Data

### 5.1 Method pembayaran: online vs offline tidak selaras
| Konteks | Nilai | |
|---|---|---|
| Kasir online (`CashierScreen`, `BackdateTransaction`, blade) | `CASH, QRIS, GOFOOD, GRABFOOD, SHOPEEFOOD, SPLIT` | |
| Sync offline (`SyncController`, `OfflineTransactionSync`, `SyncQueueProcessor`) | hanya `CASH, QRIS_STATIC` | |
- **Dampak**: Order platform (GoFood/dsb.) yang dibuat **online tidak bisa selaras ke offline/cloud** karena `firstOrCreate` gagal validasi; QRIS juga bentrok (`QRIS` vs `QRIS_STATIC`).
- **Rekomendasi**: satukan enum nilai (mis. `QRIS` konsisten; atau peta `QRIS_STATIC→QRIS`). Tambahkan dukungan GOFOOD/GRABFOOD/SHOPEEFOOD di payload sync atau setidkan `payment_method` ke nilai yang disinkronkan.

### 5.2 `platform_discount` / `platform_markup` tidak pernah diisi
- Kolom ada di model, migrasi, ERD, LLD — tapi di `processCheckout` `PLATFORM_ADJUSTMENT` hanya memaksa `finalTotal = adjustedAmount` dan **tidak menyimpan** `platform_discount`/`platform_markup`.
- Read di `FinancialReports.php:68` (`is_backdated || platform_discount != 0`) — jadi adjustment tidak terdeteksi dengan benar.
- **Rekomendasi**: isi `platform_markup` = totalAmount − adjustedAmount (atau commission rate) pada transaksi platform.

### 5.3 Scope variabel `$lp` (bug minor)
- Di `processCheckout`, `$lp` didefinisikan di dalam `try`, lalu direferensi dengan `isset($lp)` **di luar try** → selalu `false` → flash loyalty pasca-checkout **tidak pernah** fire.
- **Rekomendasi**: inisialisasi `$lp = null` sebelum try.

### 5.4 Nomor struk potensi kolisi
- `'KSV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4))` → hanya 4 heksa (~65k nilai/hari). `receipt_number` punya unique constraint — kolisi melempar.
- **Rekomendasi**: pakai `Str::uuid()` atau counter per outlet, atau `substr(uniqid('', true), -8)`.

---

## 6. Inventaris & HPP (Stock Concurrency)

- `CashierScreen`: iterasi keranjang pakai `Product::find()` lalu `deductRecipeStockForCheckout()` yang memakai `lockForUpdate`. **Baik**, tapi:
  - Stock check dilakukan **per-material secara berurutan** setelah mengunci semuanya; bila satu material gagal, seluruh transaksi rollback via `DB::transaction`. Benar.
- `OfflineTransactionSync` (jalur offline langsung): pakai `Product::find()` **tanpa lock** lalu `decrement`. Race risk hanya bila offline sync paralel — biasanya tidak terjadi pada satu device. **Diterima** bila ditandai.
  - **Namun**: tidak memanggil `deductRecipeStockForCheckout` (recipeBOM tidak dipotong), hanya `current_stock` produk dikurangi. Ini **konsisten dengan dokumen** ("offline memotong stok produk, rekap recipeBOM di cloud"), tapi berarti HPP material tidak tercatat secara offline. Perlu keputusan eksplisit.
- `SyncQueueProcessor::stamp()`: mengunci `LoyaltyMember` — baik. `claimReward` tidak dilihat (tidak ditemukan di grep), perlu dipastikan idempotent.

---

## 7. Kode & Kualitas (Code Quality)

- **Gaya umum**: campuran PSR-12 (service) dan gaya padat/minified (controller `SyncController`, `OfflineSyncController`). Rentan kesalahan baca; satu kesalahan render (garbled) hampir membuat saya berpikir ada corruption file — sebenarnya tidak. **Rekomendasi**: standardisasikan format, gunakan `pint`.
- `CashierScreen` (613 baris) adalah **God Component**: state UI, cart, discount, loyalty, checkout, receipt, WhatsApp URL — semua dalam satu kelas. Fungsional, tapi `.blade.php`-nya harus `render()` query produk setiap render (tanpa paginasi). Pertimbangkan ekstrak ke service `CartCalculator` / `CheckoutService`.
- Magic number `0.45` (`hpp ?: price * 0.45`) tiga kali — tidak terdokumentasi kenapa. Ganti konstan `DEFAULT_HPP_MARGIN = 0.45`.
- `session()->flash('message', ...)` dipakai banyak, tapi tidak ada menampilkan toast di layout — perlu pastikan feedback ditunjukkan.

### 7.1 Offline JS (`repository.js`)
- Schema v4, migration `transaction_items` dido belum dipakai penuh; `replaceAl`l menggunakan `id` key path. **Indek `transaction_items` tidak diasosiasikan ke transaksi** (hanya key `id`). Pertimbangkan index berelasi.
- `put/get/getAll/remove` tidak ada `try/catch` — error IndexedDB menggantungkan promise rejection tak tertangkap di caller.

---

## 8. Drift Dokumen

| Klaim | Realitas | File |
|---|---|---|
| Laravel 13 | Laravel 12 (`^12.0`) | README, HLD |
| `platform_discount` dihitung checkout | Tidak — field tidak pernah diisi | LLD, ERD, `Transaction` |
| QRIS butuh konfirmasi (statis) | Kasir online tidak paksa; offline ya | `OfflineTransactionSync`, `CashierScreen` |
| Loyalitas gift mengurangi stok | Tidak | `processCheckout` |
| TailwindCSS v4 badge | `tailwindcss ^4.0.0` ✓ | package.json |
| NativePHP Mobile / APK | Dideklarasikan, tidak diverifikasi di deps | HLD |

---

## 9. Cakupan Uji (Test Suite)

- Pest 4 (`tests/Feature/*`, `tests/Unit/HppCalculatorTest.php`).
- `RbacSecurityTest.php` tetap memakai **PHPUnit class style** (`extends TestCase`, `public function test...`) — campuran gaya; pest config mungkin error. Standardisasikan.
- Cakupan baik: RBAC, SyncQueueProcessor (conflict/upsert), LoyaltyParity, CampaignDiscount, HPP/margin, offline hardening, route SEO.
- **Tidak ada unit test untuk `processCheckout` (gift stock, split payment, QRIS confirm)**. Ini proyek inti yang paling perlu.
- E2E Playwright (`tests/e2e/`) + screenshots Android/iOS/mobile-web/desktop — solid UX coverage.

---

## 10. Kumpulan Tindakan (Action Items)

### 🔴 Tinggi (before release)
1. **Hash PIN di `Register.php`** (dan validasi `pin` hashed di semua path).
2. **Hapus default creds `password123`/`123456` dari `KasivaProductionSeeder`**; pakai seeder dev terpisah.
3. **Tambahan konfirmasi pembayaran QRIS** + set `payment_confirmed_manually`.
4. **Kurangi stok reward product** di `processCheckout`.
5. **Validasi split payment** jumlahnya.
6. **Perbaiki scope `$lp`** sehingga flash loyalitas berfungsi.

### 🟡 Sedang
7. Satukan enum `payment_method` online & offline.
8. Isi `platform_discount`/`platform_markup` pada checkout platform.
9. Ganti nomor struk jadi unik (uuid/counter).
10. Standardisasikan format kode (`pint --dirty`) — terutama controller.
11. Ekstrak logika checkout ke service (kurangi ukuran `CashierScreen`).
11. Ganti magic `0.45` dengan konstanta bernama.

### 🟢 Rendah / Dok
12. Samakan versi framework di README/HLD → Laravel 12.
13. Standardisasikan gaya tes (Pest atau PHPUnit) secara konsisten.
14. Tambahkan unit test `processCheckout`.
15. Dokumendasikan asumsi offline resepBOM (recipe deduction deferred to cloud).

---

## 11. Kesimpulan

Kasiva memiliki fondasi arsitektur yang kuat (offline-first, RBAC defense-in-depth, HPP atomic, retry queue). **Namun ada tiga "landmine" operasional** sebelum produksi: PIN plaintext, QRIS tak terkonfirmasi, dan loyalty gift tak mengurangi stok — ketiganya berpotensi menyebabkan kerugian uang di kasir. Selain itu **gap antara dokumen dan implementasi** (platform_discount, payment_method enum, versi Laravel) perlu diselaraskan agar tim tidak keliru. Dengan memperbaiki 6 action item prioritas-tinggi, aplikasi siap untuk release.

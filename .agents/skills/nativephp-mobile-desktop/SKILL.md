---
name: nativephp-mobile-desktop
description: Panduan integrasi NativePHP Mobile (Android/iOS) & Desktop, hardware bridge (ESC/POS thermal printer, camera barcode), dan window configuration untuk Kasiva POS.
---

# NativePHP Mobile & Desktop Skill

## 1. Konfigurasi App & Window (`config/nativephp.php`)
- **App ID**: `com.kasiva.pos`
- **Name**: `Kasiva POS`
- **Orientation**: Portrait (Mobile) / Landscape Responsive (Tablet & Desktop).
- **Permissions**: Bluetooth (Printer Thermal), Camera (Barcode Scanner).

## 2. Hardware Bridging Guidelines
- **Thermal Receipt Printing**: Gunakan driver direct ESC/POS Bluetooth/USB untuk pencetakan struk instan tanpa dialog browser.
- **SQLite Embedded DB**: Pastikan koneksi `sqlite` default mengarah ke database lokal terenkripsi di dalam container NativePHP.
- **Offline Sync Queue**: Selalu masukkan transaksi ke antrean lokal sebelum mencoba mem-push ke REST API cloud server.

## 3. Laravel 13 Migration Roadmap (Last updated: 2026-08-28)

**Current state**: Kasiva runs on Laravel 12.66 + nativephp/electron ^1.3 (Desktop wrapper).
- `nativephp/electron 1.3.0` (rilis 2025-09-04) hanya support `laravel/framework: ^10.0|^11.0|^12.0` — L13 belum disupport.
- `nativephp/mobile 4.2.0` sudah L13-ready (`^10.0|^11.0|^12.0|^13.0`), tapi Kasiva belum pakai paket ini.

**NativePHP/electron L13 sinyal**:
- PR #264 "Laravel 13.x Compatibility" (automated by Laravel Shift, 2026-03-05) **closed 2026-05-27 tanpa merge** — indikasi NativePHP maintainer belum menerima perubahan L13 otomatis; perlu rebase manual.
- Total 0 issue terbuka yang secara eksplisit mention "Laravel 13" di NativePHP/electron.

**Laravel 13.x advisories yang masih hangat** (per 2026-08-28):
- `PKSA-3r5d-mb8f-1qw9` (GHSA-5vg9-5847-vvmq) — CRLF injection di default email rule. Affected: `<12.60.0|>=13.0.0,<=13.9.0`. Severity: high. Fixed in L13.10.0.
- `PKSA-m5cs-t1y6-qpcs` (GHSA-crmm-hgp2-wgrp) — Temporary Signed URL Path Confusion. Affected: `<12.61.1|>=13.0.0,<13.12.0`. Severity: medium. Fixed in L13.12.0.
- `CVE-2026-48019` — same root cause, blanket advisory untuk semua affected majors (L9–L13).
- `CVE-2026-39976` — Laravel Passport OAuth2 bypass (L13.0.0–<13.7.1). Kasiva tidak pakai Passport, tapi kompatibilitas paket lain bisa terdampak.

**Rekomendasi decision rule** (urutan):
1. **Jangan migrasi ke L13 selama `nativephp/electron` masih support max L12.** Kasiva butuh desktop wrapper hari ini; migrasi paksa = desktop app tidak akan build.
2. **Pivot ke `nativephp/mobile` 4.2+ (Android/iOS only)** = opsi kalau siap tinggalkan desktop wrapper. nativephp/mobile sudah L13-ready.
3. **Atau tunggu** sampai `nativephp/electron` rilis versi yang declare `illuminate/contracts: ^13`. Pantau changelog di https://github.com/NativePHP/electron/releases.
4. **Patch L12 minimal** ke ≥12.61.1 agar tercover dua advisories di atas sambil menunggu roadmap.

**Checklist sebelum migrasi L13** (saat waktunya tiba):
- [ ] nativephp/electron rilis versi yang support L13 (cek Packagist `require:illuminate/contracts: ^13`)
- [ ] Semua paket Kasiva kompatibel L13 — jalankan `composer why-not laravel/framework ^13.0` di environment staging
- [ ] L13 patch ≥13.12.0 (cover advisories)
- [ ] `php artisan test` hijau di runtime L13
- [ ] NativePHP build sukses di Windows/macOS (Desktop) dan/atau Android/iOS (Mobile)
- [ ] Dokumen PRD/HLD/Vision/LLD di-update (lihat commit 9336772 untuk pola)
- [ ] Review `docs/REVIEW_KOMPREHENSIF.md` jika ada — update versi L di sana juga

**Tools monitoring**: subscribe GitHub release `NativePHP/electron` (tombol "Watch → Releases only") agar dapat notifikasi saat ada rilis baru.


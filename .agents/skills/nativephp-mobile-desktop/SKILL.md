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

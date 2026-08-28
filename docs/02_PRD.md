# Product Requirements Document (PRD)

## Kasiva — Sistem Kasir Modern Multi-Platform (Point of Sale)

| Metadata | Detail |
|---|---|
| **Nama Produk** | **Kasiva** |
| **Versi Produk** | 1.0.0 (Rebranded & Rearchitected) |
| **Tanggal Dokumen** | 2026-08-09 |
| **Status Dokumen** | Official Specification / Product Management Benchmark |
| **Tech Stack Utama** | **Laravel 12** + **Livewire 4** + **TailwindCSS v4** + **NativePHP (Desktop & Mobile SuperNative)** |
| **Database Model** | Dual Storage: SQLite Lokal (Client Kasir) & PostgreSQL (Cloud Server SaaS) |

---

## 1. Ringkasan Eksekutif

**Kasiva** adalah aplikasi Point of Sale (POS) SaaS multi-platform yang dirancang khusus untuk UMKM (Usaha Mikro, Kecil, dan Menengah) dan sektor F&B / Retail di Indonesia. Aplikasi ini mengadopsi arsitektur **offline-first** dengan engine SQLite lokal pada perangkat kasir yang tersambung ke cloud server PostgreSQL melalui mekanisme sinkronisasi latar belakang (*background sync queue*).

Kasiva mengintegrasikan manajemen produk, transaksi kasir berkecepatan tinggi, perhitungan HPP otomatis berbasis *moving average cost* dari resep bahan baku, program loyalitas pelanggan, kampanye promosi, laporan keuangan real-time, serta manajemen multi-outlet.

---

## 2. Brand Identity & Design Tokens Kasiva

| Token Brand | Hex Code | Peruntukan UI |
|---|---|---|
| **Kasiva Navy** | `#272D48` | Warna utama navbar, sidebar, header, dan latar belakang layout dark |
| **Kasiva Blue-violet** | `#505B93` | Warna sekunder, garis batas komponen (border), divider container |
| **Kasiva Teal** | `#00AAA6` | Warna aksen utama (Primary Action CTA, Tombol Bayar, Highlight Icon) |
| **Kasiva Periwinkle** | `#8696ED` | Warna aksen lembut (Badge SKU, status hover, tag kategorial) |
| **Kasiva Cyan-teal** | `#3EDAD7` | Warna aksen cerah (Indikator status aktif, display nominal harga/kembalian) |

### Logo Assets Resmi (Diakses dari `public/images/`):
- `public/images/kasiva-logo-full.png`: Logo lengkap (Icon 'K' + Wordmark "Kasiva")
- `public/images/kasiva-logo-icon.png`: Ikon mark 'K' dengan bentuk struk & centang
- `public/images/kasiva-logo-wordmark.png`: Teks wordmark Kasiva

---

## 3. Target Pengguna (User Personas)

| Persona | Deskripsi | Kebutuhan Utama |
|---|---|---|
| **Pemilik Usaha (Owner)** | Pemilik kafe / restoran / toko retail | Dashboard analisis penjualan, laporan HPP & laba kotor, manajemen staff & multi-outlet |
| **Kasir (Cashier)** | Staf garda depan di titik penjualan | Input transaksi secepat mungkin (<3 detik), cetak struk fisik/digital, scan QR member |
| **Manager Outlet** | Pengelola operasional cabang | Monitoring stok bahan baku, restok bahan baku, approval pengeluaran operasional |

---

## 4. Kebutuhan Fungsional (Functional Requirements)

### 4.1 Modul Autentikasi & Hak Akses (F-AUTH)
- **F-AUTH-01 (Registrasi & Verifikasi)**: Pendaftaran akun pemilik usaha dengan verifikasi OTP via email.
- **F-AUTH-02 (Login Multi-User)**: Autentikasi staf kasir/manager dengan PIN/Email.
- **F-AUTH-03 (Role-Based Access Control)**: Sistem hak akses granular (Owner, Manager, Cashier) dengan permission terisolasi.

### 4.2 Modul Transaksi & Kasir POS (F-POS)
- **F-POS-01 (Keranjang Belanja)**: Tambah, sesuaikan jumlah, pilih varian/modifier, dan hapus item dari keranjang kasir.
- **F-POS-02 (Kalkulasi HPP Resep)**: Kalkulasi HPP (COGS) otomatis saat checkout berdasarkan resep bahan baku per porsi.
- **F-POS-03 (Metode Pembayaran)**: Mendukung Pembayaran Tunai (kalkulasi kembalian otomatis), QRIS, dan Split Payment.
- **F-POS-04 (Struk Kasiva)**: Struk transaksi otomatis dengan nomor unik (`KSV-YYYYMMDD-XXXX`), logo Kasiva, dan QR code.
- **F-POS-05 (Offline Checkout)**: Transaksi tersimpan 100% aman di SQLite lokal saat koneksi internet terputus.

### 4.3 Modul Inventaris & HPP Moving Average (F-INV)
- **F-INV-01 (Manajemen Bahan Baku)**: Pencatatan stok bahan baku (unit: ml, gram, pcs) dan kalkulasi *moving average unit cost*:
  $$\text{New Avg Cost} = \frac{(\text{Current Stock} \times \text{Current Avg Cost}) + (\text{New Stock} \times \text{Purchase Price})}{\text{Current Stock} + \text{New Stock}}$$
- **F-INV-02 (Resep Produk)**: Pemetaan produk ke 1 atau lebih bahan baku dengan kuantitas porsi.
- **F-INV-03 (Auto Stock Deduction)**: Stok produk dan bahan baku berkurang secara otomatis dan atomic begitu checkout berhasil.

### 4.4 Modul Pemasaran & Loyalitas (F-MKT)
- **F-MKT-01 (Program Stamp Loyalty)**: Pencatatan stamp otomatis per transaksi yang memenuhi minimum pembelian.
- **F-MKT-02 (Diskon & Promo)**: Diskon persen, nominal tetap, atau promo paket bundle.
- **F-MKT-03 (Member QR Card)**: Integrasi pendaftaran member via QR code digital.

### 4.5 Modul Keuangan & Laporan (F-FIN)
- **F-FIN-01 (Laporan Penjualan)**: Grafik pendapatan, total HPP, laba kotor, dan metode pembayaran favorit.
- **F-FIN-02 (Manajemen Pengeluaran)**: Pencatatan biaya operasional (gaji, sewa, listrik/air, restok).
- **F-FIN-03 (Export Laporan)**: Export data transaksi & laporan keuangan ke format PDF dan Excel (.xlsx).

### 4.6 Modul Sinkronisasi Latar Belakang (F-SYNC)
- **F-SYNC-01 (Offline Sync Queue)**: Perubahan data transaksi offline dimasukkan ke antrean `sync_queue` di SQLite lokal.
- **F-SYNC-02 (Background Push & Retry)**: Push otomatis ke cloud server saat internet aktif dengan *exponential backoff retry*.
- **F-SYNC-03 (Bidirectional Master Pull)**: Sinkronisasi 2 arah untuk menarik update master produk dan harga dari cloud.

---

## 5. Kebutuhan Non-Fungsional (Non-Functional Requirements)

| ID | Kategori | Kebutuhan Spasial / Kinerja |
|---|---|---|
| **NF-01** | **Performance** | Operasi kasir lokal (tambah item & checkout) **< 100ms** di SQLite lokal. |
| **NF-02** | **Availability** | Aplikasi dapat beroperasi **100% offline** tanpa batasan durasi. |
| **NF-03** | **Security** | Enkripsi data lokal, hashing password bcrypt, HTTPS TLS 1.3+. |
| **NF-04** | **Multi-Platform** | Tampilan responsif di Web, Mobile Web, Android APK, iOS IPA, dan Desktop App (.exe/.dmg). |
| **NF-05** | **Data Integrity** | Transaksi basis data menggunakan DB Transaction untuk menjamin konsistensi stok & laporan. |

---

## 6. Halaman & Routing Utama Aplikasi Kasiva

1. `/` (Kasir POS Utama) — `App\Livewire\Pos\CashierScreen`
2. `/history` (Riwayat Transaksi & Struk) — `App\Livewire\History\TransactionHistory`
3. `/expenses` (Pengeluaran Operasional) — `App\Livewire\Expenses\ExpenseManager`
4. `/reports` (Laporan Keuangan & HPP) — `App\Livewire\Reports\FinancialReports`
5. `/settings` (Pusat Setelan Kasiva) — `App\Livewire\Settings\SettingsHub`

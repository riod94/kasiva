---
name: laravel-livewire-expert
description: Standar pengembangan & arsitektur Laravel 13, Livewire 4, Eloquent ORM, serta kalkulasi HPP dan transaksi DB atomic untuk Kasiva.
---

# Laravel 13 & Livewire 4 Expert Skill

## 1. Prinsip Utama & Arsitektur Kode
- **Single Responsibility Component**: Setiap komponen Livewire hanya mengurus satu domain (contoh: `CashierScreen` untuk POS, `TransactionHistory` untuk riwayat, `ExpenseManager` untuk pengeluaran).
- **Atomic Database Transactions**: Selalu gunakan `DB::transaction()` saat pemotongan stok bahan baku & pembentukan transaksi penjualan untuk mencegah kebocoran/ketidakkonsistenan data stok.
- **Strict Casts & Null Safety**: Selalu definisikan `$casts` pada Eloquent Model untuk angka numerik (`float`, `integer`, `boolean`, `datetime`).

## 2. HPP Moving Average Calculation
Gunakan `HppCalculatorService` untuk mengkalkulasi harga rata-rata bahan baku saat restock:
$$\text{New Avg Cost} = \frac{(\text{Current Stock} \times \text{Current Avg Cost}) + (\text{New Stock} \times \text{Purchase Price})}{\text{Current Stock} + \text{New Stock}}$$

## 3. Komponen Livewire 4 & Performance
- Gunakan `wire:model.live.debounce.250ms` untuk input pencarian produk dan kalkulasi nominal bayar.
- Hindari N+1 Query dengan selalu memangggil `with()` atau `loadMissing()` pada relasi Eloquent (`category`, `materials`, `variants.options`).

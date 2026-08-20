---
name: pos-domain-analytics
description: Spesifikasi logika bisnis POS, 4-tier margin health analytics, format struk digital KSV, dan platform delivery price adjustments.
---

# POS Domain & Business Logic Skill

## 1. Indikator Kesehatan Margin 4-Tier
Setiap produk dihitung margin keuntungan bersihnya berdasarkan harga jual dan HPP resep:
$$\text{Margin \%} = \frac{\text{Price} - \text{HPP}}{\text{Price}} \times 100$$

| Klasifikasi | Rentang Margin | Badge Style | Makna Bisnis |
|---|---|---|---|
| **Kritis** | `< 30%` | `bg-red-100 text-red-700 border-red-200` | Profit terlalu tipis, berisiko rugi jika ada fluktuasi bahan baku |
| **Tipis** | `30% - 44%` | `bg-amber-100 text-amber-700 border-amber-200` | Margin wajar tetapi perlu pengawasan biaya bahan baku |
| **Sehat** | `45% - 71%` | `bg-emerald-100 text-emerald-700 border-emerald-200` | Margin ideal untuk usaha F&B & Retail |
| **Optimal** | `≥ 72%` | `bg-indigo-100 text-indigo-700 border-indigo-200` | Profitabilitas sangat tinggi |

## 2. Format Struk Digital Kasiva
- **Receipt Numbering Format**: `KSV-YYYYMMDD-XXXX` (contoh: `KSV-20260812-A9F4`).
- **Kanal Pembayaran**: `CASH`, `QRIS`, `SPLIT`, `GOFOOD`, `GRABFOOD`, `SHOPEEFOOD`.
- **Platform Adjustment**: Catat komisi/markup harga khusus platform delivery secara terpisah agar perhitungan omset tetap akurat.

## 3. Laporan Keuangan 3-Level Profit
1. **Omset Total**: Total seluruh nominal transaksi.
2. **Gross Profit (Laba Kotor)**: $\text{Omset Total} - \text{Total HPP Resep}$.
3. **Net Profit (Laba Bersih)**: $\text{Gross Profit} - \text{Total Pengeluaran Operasional}$.

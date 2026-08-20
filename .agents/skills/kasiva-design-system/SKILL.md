---
name: kasiva-design-system
description: Standar token warna resmi Kasiva, komponen UI responsif (Bottom Nav 5-Tab, Floating Cart Sheet), dan panduan aset logo resmi.
---

# Kasiva Design System Skill

## 1. Official Brand Palette Tokens
| Token Brand | Hex Code | Utility Class | Penggunaan |
|---|---|---|---|
| **Kasiva Navy** | `#272D48` | `bg-[#272D48]`, `text-white` | Latar belakang utama layout, header navbar, container dark |
| **Kasiva Blue-violet** | `#505B93` | `border-[#505B93]`, `bg-[#505B93]` | Garis batas komponen (border), divider, elemen sekunder |
| **Kasiva Teal** | `#00AAA6` | `bg-[#00AAA6]`, `text-[#00AAA6]` | Tombol aksen utama (Primary Action CTA, Tombol Bayar) |
| **Kasiva Periwinkle** | `#8696ED` | `text-[#8696ED]` | Aksen badge SKU, tag kategorial, status hover |
| **Kasiva Cyan-teal** | `#3EDAD7` | `text-[#3EDAD7]` | Indikator status aktif, display nominal harga/kembalian |

## 2. Aset Logo Resmi (`public/images/`)
- `public/images/kasiva-logo-full.png`: Logo lengkap untuk Header Navbar & Struk Digital.
- `public/images/kasiva-logo-icon.png`: Favicon, App Icon, & Avatar Kasir.
- `public/images/kasiva-logo-wordmark.png`: Footer & Dokumentasi Resmi.

## 3. Responsive Layout Rules
- **Mobile Viewport**: Gunakan Fixed Bottom Navigation Bar (5 Tab: Kasir, Riwayat, Pengeluaran, Laporan, Setelan) & Floating Cart Sheet.
- **Desktop/Tablet Viewport**: Tampilkan Right Sidebar (384px) untuk keranjang belanja & grid produk 3-4 kolom.
- **Touch Targets**: Pastikan tombol memiliki area sentuh minimal 40px × 40px untuk pengoperasian kasir di layar sentuh.

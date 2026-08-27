<?php

declare(strict_types=1);

namespace App\Support;

/**
 * LandingContent — data layer tunggal untuk copy landing page Kasiva.
 *
 * Tujuan:
 * - Pisahkan copy dari markup Blade agar mudah diuji, di-i18n, dan di-versioning.
 * - Menjadi single source of truth untuk SoftwareApplication.featureList dan FAQPage.mainEntity.
 * - Tidak menyimpan state, tidak baca DB — return array statis.
 */
final class LandingContent
{
    /**
     * 6 fitur utama yang ditampilkan di feature grid.
     * Accent: teal | violet | cyan — dipetakan ke border & gradient kartu.
     *
     * @return list<array{icon:string,title:string,desc:string,accent:string}>
     */
    public static function features(): array
    {
        return [
            [
                'icon' => 'store',
                'title' => 'Kasir secepat antrean Anda',
                'desc' => 'Multi-cart, pencarian instan, varian produk, dan checkout sentuh yang tetap responsif saat jam sibuk.',
                'accent' => 'teal',
            ],
            [
                'icon' => 'package',
                'title' => 'HPP resep yang selalu akurat',
                'desc' => 'Harga bahan, moving average, dan pemakaian resep dihitung otomatis untuk setiap produk terjual.',
                'accent' => 'violet',
            ],
            [
                'icon' => 'chart-bar',
                'title' => 'Profit yang benar-benar terlihat',
                'desc' => 'Pisahkan omzet, modal restok, laba kotor, beban, dan profit bersih tanpa spreadsheet manual.',
                'accent' => 'cyan',
            ],
            [
                'icon' => 'gift',
                'title' => 'Pelanggan punya alasan kembali',
                'desc' => 'Member QR, stempel digital, reward, promo, dan struk WhatsApp dalam satu alur transaksi.',
                'accent' => 'violet',
            ],
            [
                'icon' => 'shield',
                'title' => 'Kontrol staf tanpa celah',
                'desc' => 'Peran Owner, Manager, dan Kasir dilindungi permission untuk setiap area operasional penting.',
                'accent' => 'teal',
            ],
            [
                'icon' => 'wifi-off',
                'title' => 'Internet mati, jualan tetap jalan',
                'desc' => 'Mode offline-first menyimpan operasi lokal dan menyinkronkannya kembali saat koneksi tersedia.',
                'accent' => 'cyan',
            ],
        ];
    }

    /**
     * 3 langkah cara kerja — dipetakan juga ke JSON-LD HowTo.
     *
     * @return list<array{number:string,icon:string,title:string,desc:string}>
     */
    public static function howItWorks(): array
    {
        return [
            [
                'number' => '01',
                'icon' => 'store',
                'title' => 'Buat outlet',
                'desc' => 'Daftar, isi profil outlet, dan undang staf sesuai perannya.',
            ],
            [
                'number' => '02',
                'icon' => 'package',
                'title' => 'Susun menu & resep',
                'desc' => 'Masukkan produk, bahan, harga beli, varian, dan resep per porsi.',
            ],
            [
                'number' => '03',
                'icon' => 'wallet',
                'title' => 'Mulai berjualan',
                'desc' => 'Terima pembayaran dan lihat stok serta profit berubah secara otomatis.',
            ],
        ];
    }

    /**
     * 3 paket harga — saat ini Starter aktif (Rp 0), sisanya placeholder arah produk.
     *
     * @return list<array{name:string,price:string,priceSuffix:string,badge:?string,cta:string,comingSoon:bool,highlights:list<string>,note:?string}>
     */
    public static function pricingPackages(): array
    {
        return [
            [
                'name' => 'Kasiva Starter',
                'price' => 'Rp 0',
                'priceSuffix' => 'untuk early access',
                'badge' => 'EARLY ACCESS',
                'cta' => 'Mulai sekarang',
                'comingSoon' => false,
                'highlights' => [
                    'POS multi-cart',
                    'Produk & resep',
                    'Stok bahan baku',
                    'HPP otomatis',
                    'Laporan profit',
                    'Member & loyalitas',
                    'Pembayaran lengkap',
                    'Mode offline',
                ],
                'note' => 'Tidak ada biaya setup atau kartu kredit. Informasi paket dapat berubah saat Kasiva keluar dari fase early access dan akan diinformasikan secara transparan.',
            ],
            [
                'name' => 'Kasiva Pro',
                'price' => 'Segera hadir',
                'priceSuffix' => 'paket berbayar opsional',
                'badge' => null,
                'cta' => 'Daftar tunggu',
                'comingSoon' => true,
                'highlights' => [
                    'Seluruh fitur Starter',
                    'Multi-outlet',
                    'Integrasi marketplace lanjutan',
                    'Laporan lanjutan & forecast',
                    'Priority support',
                ],
                'note' => null,
            ],
            [
                'name' => 'Kasiva Enterprise',
                'price' => 'Hubungi kami',
                'priceSuffix' => 'untuk rantai bisnis',
                'badge' => null,
                'cta' => 'Bicara dengan tim',
                'comingSoon' => true,
                'highlights' => [
                    'Seluruh fitur Pro',
                    'SLA & on-site training',
                    'Konsultan profit khusus',
                    'Integrasi akuntansi (Xero/Jurnal)',
                    'White-label opsional',
                ],
                'note' => null,
            ],
        ];
    }

    /**
     * 4 platform — web aktif, sisanya placeholder arah produk (lihat docs/NATIVEPHP_SETUP.md).
     *
     * @return list<array{icon:string,name:string,desc:string,status:string}>
     */
    public static function platforms(): array
    {
        return [
            ['icon' => 'home', 'name' => 'Web PWA', 'desc' => 'Akses dari browser desktop & mobile, tanpa install.', 'status' => 'Tersedia'],
            ['icon' => 'phone', 'name' => 'Android', 'desc' => 'Native app untuk kasir mobile, integrasi printer Bluetooth.', 'status' => 'Segera'],
            ['icon' => 'phone', 'name' => 'iOS', 'desc' => 'Native app untuk iPhone & iPad, fokus performa kasir.', 'status' => 'Segera'],
            ['icon' => 'printer', 'name' => 'Desktop', 'desc' => 'NativePHP desktop untuk kasir tetap dengan printer USB.', 'status' => 'Segera'],
        ];
    }

    /**
     * 10 FAQ — field `slug` jadi fragment anchor (#faq-{slug}).
     *
     * @return list<array{slug:string,q:string,a:string}>
     */
    public static function faqs(): array
    {
        return [
            [
                'slug' => 'offline',
                'q' => 'Apakah Kasiva bisa dipakai tanpa internet?',
                'a' => 'Bisa. Alur kasir utama dirancang offline-first. Data penting disimpan pada perangkat dan antrean perubahan dapat disinkronkan kembali ketika internet tersedia.',
            ],
            [
                'slug' => 'hpp',
                'q' => 'Bagaimana Kasiva menghitung HPP produk?',
                'a' => 'Kasiva menghubungkan produk dengan resep dan bahan baku. Harga rata-rata bahan diperbarui saat restok, lalu pemakaian resep dikurangi otomatis ketika checkout berhasil.',
            ],
            [
                'slug' => 'pembayaran',
                'q' => 'Apakah pembayaran QRIS dan platform delivery didukung?',
                'a' => 'Ya. Kasiva mendukung tunai, QRIS, split payment, serta pencatatan GoFood, GrabFood, dan ShopeeFood berikut penyesuaian markup atau diskonnya.',
            ],
            [
                'slug' => 'rbac',
                'q' => 'Apakah staf dapat dibatasi aksesnya?',
                'a' => 'Ya. Sistem role dan permission membatasi menu serta tindakan sensitif sesuai tanggung jawab Owner, Manager, atau Kasir.',
            ],
            [
                'slug' => 'struk',
                'q' => 'Bisakah saya mengirim struk digital?',
                'a' => 'Bisa. Transaksi dapat dibuatkan struk untuk printer thermal maupun dibagikan secara digital melalui WhatsApp.',
            ],
            [
                'slug' => 'migrasi',
                'q' => 'Bagaimana cara migrasi data dari POS lama saya?',
                'a' => 'Tim onboarding membantu impor produk, stok, dan member dari CSV atau POS lain. Kontak kami dari halaman Tentang untuk jadwalkan migrasi.',
            ],
            [
                'slug' => 'multi-outlet',
                'q' => 'Apakah Kasiva mendukung multi-outlet?',
                'a' => 'Paket Pro dan Enterprise mendukung banyak outlet dalam satu akun Owner dengan laporan konsolidasi. Starter disiapkan untuk satu outlet.',
            ],
            [
                'slug' => 'printer',
                'q' => 'Printer thermal apa saja yang didukung?',
                'a' => 'Printer ESC/POS 58mm & 80mm melalui Bluetooth, USB, atau jaringan. Integrasi diuji pada merek populer di Indonesia dan dapat ditambah sesuai kebutuhan.',
            ],
            [
                'slug' => 'keamanan-data',
                'q' => 'Apakah data saya aman jika perangkat hilang?',
                'a' => 'Data lokal terenkripsi dan otomatis dicadangkan ke cloud saat online. Owner dapat menghapus sesi perangkat dari Staf & Peran kapan saja.',
            ],
            [
                'slug' => 'harga',
                'q' => 'Bagaimana model harga setelah early access berakhir?',
                'a' => 'Starter akan tetap gratis dengan fitur inti. Paket Pro dan Enterprise akan diumumkan minimal 30 hari sebelum early access berakhir dan diinformasikan ke semua pengguna.',
            ],
        ];
    }

    /**
     * 3 studi kasus placeholder — gunakan inisial SVG, label "Coming soon" agar tidak overclaim.
     *
     * @return list<array{initials:string,name:string,role:string,quote:string,comingSoon:bool}>
     */
    public static function testimonials(): array
    {
        return [
            [
                'initials' => 'AR',
                'name' => 'Andi R.',
                'role' => 'Pemilik Kafe, Jakarta',
                'quote' => 'Stok bahan dan HPP otomatis membuat kami tahu profit tiap menu tanpa rekap manual.',
                'comingSoon' => true,
            ],
            [
                'initials' => 'BS',
                'name' => 'Budi S.',
                'role' => 'Manajer Rantai Kopi, Bandung',
                'quote' => 'Mode offline menjaga antrean tetap jalan walau Wi-Fi gerai sedang gangguan.',
                'comingSoon' => true,
            ],
            [
                'initials' => 'CW',
                'name' => 'Citra W.',
                'role' => 'Pemilik Toko Retail, Surabaya',
                'quote' => 'Akses Owner, Manager, Kasir terpisah jelas. Tidak ada lagi laporan yang bocor ke peran yang salah.',
                'comingSoon' => true,
            ],
        ];
    }

    /**
     * Metrik ringkas untuk trust strip — bersumber dari docs/01_VISION_&_STRATEGY.md & OFFLINE_RUNBOOK.md.
     *
     * @return list<array{value:string,label:string}>
     */
    public static function metrics(): array
    {
        return [
            ['value' => '<100ms', 'label' => 'Respon kasir'],
            ['value' => '100%', 'label' => 'Mode offline'],
            ['value' => '3-tier', 'label' => 'RBAC Owner/Manager/Kasir'],
            ['value' => 'KSV', 'label' => 'Format struk standar'],
        ];
    }
}

# KASIVA — NativePHP Setup & Mobile Integration Guide (Android & iOS)

Dokumen ini menjelaskan panduan instalasi, konfigurasi, dan kompilasi aplikasi **Kasiva** menjadi aplikasi Native Android dan iOS menggunakan **NativePHP**.

---

## 1. Prerequisites (Prasyarat Sistem)

- **PHP**: `^8.3`
- **Laravel**: `^13.0`
- **Composer**: `>= 2.6`
- **Node.js**: `>= 20.x`
- **Android Studio / SDK** (untuk kompilasi APK / Android Bundle)
- **Xcode & macOS** (untuk kompilasi iOS App Bundle)

---

## 2. Instalasi NativePHP Package

Jalankan perintah berikut di terminal proyek Kasiva:

```bash
# 1. Install paket NativePHP Mobile untuk Laravel
composer require nativephp/mobile

# 2. Jalankan perintah instalasi NativePHP
php artisan native:install
```

Perintah `native:install` akan mempublikasikan file konfigurasi `config/nativephp.php` dan aset pendukung.

---

## 3. Konfigurasi App & Window (`config/nativephp.php`)

NativePHP memungkinkan konfigurasi aspek aplikasi mobile:

```php
return [
    'version' => '1.0.0',
    'app_id' => 'com.kasiva.pos',
    'name' => 'Kasiva POS',
    'author' => 'Kasiva Technologies',

    'mobile' => [
        'orientation' => 'portrait',
        'fullscreen' => true,
        'splash_screen' => [
            'enabled' => true,
            'image' => 'public/images/splash.png',
            'background_color' => '#4338CA', // Indigo Ngepos
        ],
        'hardware_permissions' => [
            'camera' => true,      # Untuk pemindaian barcode/QR
            'bluetooth' => true,   # Untuk koneksi printer thermal POS
        ],
    ],
];
```

---

## 4. Pengujian & Servis Lokal

### Menjalankan Mode Development Native:
```bash
# Menjalankan server NativePHP lokal
php artisan native:serve
```

### Menjalankan Mode Web Mobile Responsive:
```bash
# Menjalankan server web Laravel biasa
composer dev
# Atau: php artisan serve & npm run dev
```

---

## 5. Build Aplikasi Native

### Build APK Android:
```bash
php artisan native:build android
```

### Build App Store / TestFlight iOS:
```bash
php artisan native:build ios
```

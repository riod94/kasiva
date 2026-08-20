<?php

return [
    /*
    |--------------------------------------------------------------------------
    | NativePHP Kasiva SaaS POS Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi pembungkusan Kasiva POS menjadi aplikasi Native (Android APK,
    | iOS IPA, Windows EXE, macOS DMG).
    |
    */

    'version' => '1.0.0',
    'app_id' => 'com.kasiva.pos',
    'name' => 'Kasiva POS',
    'author' => 'Kasiva Technologies',

    'mobile' => [
        'orientation' => 'portrait',
        'fullscreen' => true,
        'splash_screen' => [
            'enabled' => true,
            'image' => 'public/images/kasiva-logo-full.png',
            'background_color' => '#272D48',
        ],
        'hardware_permissions' => [
            'camera' => true,      // Untuk pemindaian barcode/QR
            'bluetooth' => true,   // Untuk printer thermal POS Bluetooth
            'storage' => true,     // Untuk penyimpanan SQLite lokal & laporan PDF
        ],
    ],

    'desktop' => [
        'width' => 1280,
        'height' => 800,
        'min_width' => 1024,
        'min_height' => 768,
        'resizable' => true,
        'title' => 'Kasiva POS — Modern Point of Sale System',
        'icon' => 'public/images/kasiva-logo-icon.png',
    ],
];

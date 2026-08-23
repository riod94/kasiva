<?php

return [
    /*
    |--------------------------------------------------------------------------
    | NativePHP Kasiva POS Configuration
    |--------------------------------------------------------------------------
    |
    | Runtime yang didukung saat ini adalah NativePHP Electron untuk desktop.
    | Dukungan Android/iOS ditambahkan hanya setelah package dan permission
    | runtime NativePHP Mobile dipasang serta diuji pada perangkat nyata.
    |
    */

    'version' => '1.0.0',
    'app_id' => 'com.kasiva.pos',
    'name' => 'Kasiva POS',
    'author' => 'Kasiva Technologies',

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

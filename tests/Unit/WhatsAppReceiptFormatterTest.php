<?php

use App\Models\Transaction;
use App\Services\WhatsAppReceiptFormatter;
use Illuminate\Support\Collection;

uses(Tests\TestCase::class);

test('membangun URL wa.me yang valid dengan teks ter-encode', function () {
    $tx = new Transaction(['receipt_number' => 'KSV-001', 'cashier_name' => 'Budi', 'payment_method' => 'CASH', 'total_amount' => 50000, 'paid_amount' => 50000, 'change_amount' => 0]);
    $tx->created_at = now();
    $tx->setRelation('items', new Collection);

    $url = WhatsAppReceiptFormatter::url($tx);

    expect($url)->toStartWith('https://api.whatsapp.com/send?text=');
    $decoded = urldecode(substr($url, strlen('https://api.whatsapp.com/send?text=')));
    expect($decoded)->toContain('*STRUK DIGITAL KASIVA POS*');
    expect($decoded)->toContain('KSV-001');
    expect($decoded)->toContain('Budi');
    expect($decoded)->toContain('Rp 50.000');
});

test('teks struk memuat baris item, diskon, total, bayar, kembali bila ada', function () {
    $item = (object) ['product_name' => 'Kopi Susu', 'quantity' => 2, 'subtotal' => 30000];
    $tx = new Transaction([
        'receipt_number' => 'KSV-002',
        'cashier_name' => 'Siti',
        'payment_method' => 'QRIS',
        'total_amount' => 27000,
        'paid_amount' => 27000,
        'change_amount' => 0,
        'discount_total' => 3000,
        'discount_note' => 'Promo 10%',
    ]);
    $tx->created_at = now();
    $tx->setRelation('items', new Collection([$item]));

    $text = WhatsAppReceiptFormatter::text($tx);

    expect($text)->toContain('Kopi Susu (2x) - Rp 30.000');
    expect($text)->toContain('DISKON: -Rp 3.000 (Promo 10%)');
    expect($text)->toContain('TOTAL: Rp 27.000');
    expect($text)->toContain('BAYAR (QRIS): Rp 27.000');
    expect($text)->not->toContain('KEMBALI');
});

test('teks struk memunculkan baris KEMBALI ketika ada change_amount > 0', function () {
    $tx = new Transaction([
        'receipt_number' => 'KSV-003',
        'cashier_name' => 'Ani',
        'payment_method' => 'CASH',
        'total_amount' => 17500,
        'paid_amount' => 20000,
        'change_amount' => 2500,
    ]);
    $tx->created_at = now();
    $tx->setRelation('items', new Collection);

    $text = WhatsAppReceiptFormatter::text($tx);

    expect($text)->toContain('KEMBALI: Rp 2.500');
});

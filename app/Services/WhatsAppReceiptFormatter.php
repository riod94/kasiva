<?php

namespace App\Services;

use App\Models\LoyaltyMember;
use App\Models\Transaction;

/**
 * Format struk digital menjadi URL WhatsApp (wa.me).
 * Pure function — tidak butuh Livewire state, sehingga mudah di-test dan di-reuse.
 */
class WhatsAppReceiptFormatter
{
    public static function url(Transaction $transaction): string
    {
        $text = self::text($transaction);

        return 'https://api.whatsapp.com/send?text='.urlencode($text);
    }

    public static function text(Transaction $transaction): string
    {
        $lines = [
            '*STRUK DIGITAL KASIVA POS*',
            'No: '.$transaction->receipt_number,
            'Tanggal: '.$transaction->created_at->format('d/m/Y H:i'),
            'Kasir: '.$transaction->cashier_name,
        ];

        if ($transaction->loyalty_member_id) {
            $member = LoyaltyMember::find($transaction->loyalty_member_id);
            if ($member) {
                $lines[] = 'Member: '.($member->name ?: $member->qr_code).' ('.$member->qr_code.')';
            }
        }

        if (($transaction->discount_note ?? '') !== '') {
            $lines[] = 'Promo: '.$transaction->discount_note;
        }

        $lines[] = '--------------------------------';
        foreach ($transaction->items as $item) {
            $lines[] = $item->product_name.' ('.$item->quantity.'x) - Rp '.number_format($item->subtotal, 0, ',', '.');
        }
        $lines[] = '--------------------------------';

        if (($transaction->discount_total ?? 0) > 0) {
            $lines[] = 'DISKON: -Rp '.number_format($transaction->discount_total, 0, ',', '.').' ('.($transaction->discount_note ?? '').')';
        }
        $lines[] = 'TOTAL: Rp '.number_format($transaction->total_amount, 0, ',', '.');
        $lines[] = 'BAYAR ('.$transaction->payment_method.'): Rp '.number_format($transaction->paid_amount, 0, ',', '.');
        if ($transaction->change_amount > 0) {
            $lines[] = 'KEMBALI: Rp '.number_format($transaction->change_amount, 0, ',', '.');
        }
        $lines[] = '';
        $lines[] = 'Terima kasih telah berbelanja di Kasiva!';

        return implode("\n", $lines);
    }
}

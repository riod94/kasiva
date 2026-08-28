<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Ekstraksi logika kalkulasi cart dari CashierScreen (God Component 764L).
 * Dipakai Livewire + bisa di-unit test.
 */
class CartCalculator
{
    public function __construct(private CampaignDiscountService $campaigns) {}

    /**
     * @param  array  $cart  list ['id','name','price','hpp','qty', ...]
     * @return array{subtotal:float,totalHpp:float,discountTotal:float,discountDetails:array,discountNote:string,total:float}
     */
    public function calculate(array $cart): array
    {
        $subtotal = 0.0;
        $totalHpp = 0.0;
        foreach ($cart as $item) {
            $subtotal += (float) ($item['price'] ?? 0) * (int) ($item['qty'] ?? 0);
            $totalHpp += (float) ($item['hpp'] ?? 0) * (int) ($item['qty'] ?? 0);
        }

        $discountTotal = 0.0;
        $discountDetails = [];
        $discountNote = '';
        try {
            $cartForDiscount = collect($cart)->map(fn ($it) => [
                'product_id' => $it['id'] ?? $it['product_id'] ?? null,
                'price' => $it['price'] ?? 0,
                'qty' => $it['qty'] ?? 0,
                'name' => $it['name'] ?? '',
            ])->values()->toArray();
            $res = $this->campaigns->calculate($cartForDiscount);
            $discountTotal = (float) ($res['total'] ?? 0);
            $discountDetails = $res['details'] ?? [];
            $discountNote = $res['note'] ?? '';
        } catch (\Throwable $e) {
            Log::warning('Kasiva CartCalculator campaign gagal', ['error' => $e->getMessage()]);
        }

        $total = max(0.0, $subtotal - $discountTotal);

        return [
            'subtotal' => $subtotal,
            'totalHpp' => $totalHpp,
            'discountTotal' => $discountTotal,
            'discountDetails' => $discountDetails,
            'discountNote' => $discountNote,
            'total' => $total,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Ekstraksi logika checkout dari CashierScreen::processCheckout().
 * Tetap memakai HppCalculatorService & LoyaltyService di dalam.
 */
class CheckoutService
{
    public const DEFAULT_HPP_RATIO = 0.45;

    public function process(
        array $cart,
        float $subtotalAmount,
        float $totalHpp,
        float $discountTotal,
        string $discountNote,
        float $totalAmount,
        string $paymentMethod,
        string $checkoutStep,
        bool $qrisConfirmed,
        float $paidAmount,
        float $splitCashAmount,
        float $splitQrisAmount,
        float $adjustedAmount,
        string $selectedPlatform,
        ?string $linkedMemberId,
        ?string $appliedRewardId,
    ): array {
        if (empty($cart)) {
            throw new \InvalidArgumentException('Keranjang kosong.');
        }

        if ($checkoutStep === 'QRIS_DISPLAY' && ! $qrisConfirmed) {
            throw new \InvalidArgumentException('Konfirmasi pembayaran QRIS wajib dicentang sebelum menyelesaikan transaksi.');
        }

        if ($checkoutStep === 'SPLIT_PAYMENT') {
            $splitSum = (float) $splitCashAmount + (float) $splitQrisAmount;
            if (abs($splitSum - $totalAmount) > 0.01) {
                throw new \InvalidArgumentException('Jumlah Tunai + QRIS harus sama dengan total tagihan (Rp '.number_format($totalAmount, 0, ',', '.').'). Saat ini Rp '.number_format($splitSum, 0, ',', '.').'.');
            }
        }

        $receiptNumber = 'KSV-'.date('Ymd').'-'.strtoupper(substr((string) Str::orderedUuid(), 0, 8));
        $user = Auth::user();
        $cashierName = $user?->name ?? 'Kasir Utama';

        $finalTotal = $totalAmount;
        $paid = (float) $paidAmount;
        $change = max(0.0, $paid - $totalAmount);
        $platformMarkup = 0.0;
        $platformDiscount = 0.0;

        if ($checkoutStep === 'PLATFORM_ADJUSTMENT') {
            $finalTotal = (float) $adjustedAmount;
            $paid = (float) $adjustedAmount;
            $change = 0.0;
            $diff = $totalAmount - $finalTotal;
            if ($diff >= 0) {
                $platformDiscount = round($diff, 2);
            } else {
                $platformMarkup = round(abs($diff), 2);
            }
        } elseif ($checkoutStep === 'QRIS_DISPLAY') {
            $paid = $totalAmount;
            $change = 0.0;
        } elseif ($checkoutStep === 'SPLIT_PAYMENT') {
            $paid = (float) $splitCashAmount + (float) $splitQrisAmount;
            $change = 0.0;
        }

        $cartSnapshot = $cart;
        $snapshotHpp = $totalHpp;
        $snapshotDiscountNote = trim(($discountNote ? $discountNote : '').($appliedRewardId ? ($discountNote ? ', ' : '').'Loyalty Reward' : ''));

        $rewardProduct = null;
        $appliedProgram = LoyaltyProgram::where('is_active', true)->first();
        if ($appliedRewardId && $appliedProgram && ($appliedProgram->reward_type ?? 'FREE_PRODUCT') === 'FREE_PRODUCT' && ! empty($appliedProgram->reward_product_id)) {
            $rewardProduct = Product::find($appliedProgram->reward_product_id);
        }

        $loyaltyProgramForFlash = $appliedProgram;
        $transaction = null;

        DB::transaction(function () use (&$transaction, $receiptNumber, $cashierName, $finalTotal, $snapshotHpp, $paid, $change, $linkedMemberId, $cartSnapshot, $rewardProduct, $platformMarkup, $platformDiscount, $qrisConfirmed, $paymentMethod, $checkoutStep) {
            $isQris = $paymentMethod === 'QRIS' || $checkoutStep === 'QRIS_DISPLAY';
            $transaction = Transaction::create([
                'receipt_number' => $receiptNumber,
                'payment_method' => $paymentMethod,
                'total_amount' => $finalTotal,
                'total_hpp' => $snapshotHpp + ($rewardProduct ? (float) ($rewardProduct->hpp ?: $rewardProduct->price * self::DEFAULT_HPP_RATIO) : 0),
                'discount_total' => app(CartCalculator::class)->calculate($cartSnapshot)['discountTotal'] ?? 0,
                // discount_note & discount_total sudah ada di CartCalculator; pakai snapshot aktual Livewire kalau ada — fallback di atas aman
                'discount_note' => null, // akan di-override di penutup transaction jika perlu
                'loyalty_member_id' => $linkedMemberId,
                'paid_amount' => $paid,
                'change_amount' => $change,
                'platform_markup' => $platformMarkup,
                'platform_discount' => $platformDiscount,
                'payment_confirmed_manually' => $isQris ? (bool) $qrisConfirmed : false,
                'cashier_name' => $cashierName,
                'sync_status' => 'SYNCED',
            ]);
            // isi discount_note/total yang benar dari snapshot closure variable
            // workaround: update setelah create agar tidak bentrok dengan kalkulasi di atas
            // (dipakai Livewire snapshot discountNote/discountTotal asli)
            // -> caller akan sync ulang via Livewire state; di sini simpan apa adanya 0 jika race

            foreach ($cartSnapshot as $item) {
                $product = Product::find($item['id']);
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product ? $product->id : $item['id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'unit_hpp' => $item['hpp'],
                    'quantity' => $item['qty'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);
                if ($product) {
                    app(HppCalculatorService::class)->deductRecipeStockForCheckout($product, $item['qty']);
                }
            }
            if ($rewardProduct) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $rewardProduct->id,
                    'product_name' => '[GIFT] '.$rewardProduct->name,
                    'unit_price' => 0,
                    'unit_hpp' => (float) ($rewardProduct->hpp ?: $rewardProduct->price * self::DEFAULT_HPP_RATIO),
                    'quantity' => 1,
                    'subtotal' => 0,
                ]);
                app(HppCalculatorService::class)->deductRecipeStockForCheckout($rewardProduct, 1);
            }
        });

        // patch discount_total/note ke nilai snapshot Livewire yang lebih akurat
        if ($transaction) {
            try {
                $transaction->update([
                    'discount_total' => $discountTotal,
                    'discount_note' => $discountNote ?: null,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Kasiva CheckoutService patch discount gagal', ['error' => $e->getMessage()]);
            }
        }

        // Post-checkout loyalty (non-kritis)
        if ($transaction && $linkedMemberId) {
            try {
                $lp = LoyaltyProgram::where('is_active', true)->first();
                $loyaltyProgramForFlash = $lp;
                if ($lp) {
                    $cartProductIds = array_map(fn ($it) => (string) $it['id'], array_values($cartSnapshot));
                    $eligible = LoyaltyService::isStampEligible((float) $subtotalAmount, $discountTotal > 0, $cartProductIds, $lp);
                    if ($eligible) {
                        $m = LoyaltyMember::find($linkedMemberId);
                        if ($m) {
                            LoyaltyService::addStamp($m, $lp, $transaction->id);
                            LoyaltyService::checkAndCreateReward($m, $lp);
                        }
                    }
                }
                if ($appliedRewardId) {
                    LoyaltyService::claimReward($appliedRewardId, $transaction->id);
                }
            } catch (\Throwable $e) {
                Log::warning('Kasiva loyalty post-checkout gagal', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id ?? null]);
            }
        }

        return [
            'transaction' => $transaction ? $transaction->load('items') : null,
            'loyaltyProgramForFlash' => $loyaltyProgramForFlash,
        ];
    }
}

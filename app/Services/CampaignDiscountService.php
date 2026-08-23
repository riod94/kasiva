<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignItem;
use App\Models\CampaignReward;

/**
 * Port dari Ngepos src/stores/cart.ts::calculateDiscounts()
 * Mendukung BULK_DISCOUNT, BUNDLE, BUY_X_GET_Y
 */
class CampaignDiscountService
{
    /**
     * @param array $cartItems  ['product_id' => string, 'price' => float, 'qty' => int, 'name' => string][]
     * @return array{total: float, details: array{name:string,amount:float}[], note: string}
     */
    public function calculate(array $cartItems): array
    {
        if (empty($cartItems)) {
            return ['total' => 0, 'details' => [], 'note' => ''];
        }

        $campaigns = Campaign::where('is_active', true)
            ->orderByDesc('priority')
            ->with(['items', 'rewards'])
            ->get();

        if ($campaigns->isEmpty()) {
            return ['total' => 0, 'details' => [], 'note' => ''];
        }

        $totalDiscount = 0;
        $details = [];
        $usedQty = []; // product_id => used qty

        foreach ($campaigns as $camp) {
            $reward = $camp->rewards->first();
            // fallback: campaign menyimpan reward di kolom sendiri (CampaignManager lama)
            if (!$reward) {
                if (empty($camp->reward_type) && empty($camp->reward_value)) continue;
                $reward = (object)['reward_type' => $camp->reward_type, 'reward_value' => (float)$camp->reward_value];
            }

            if ($camp->type === 'BULK_DISCOUNT') {
                foreach ($cartItems as $item) {
                    $isTarget = $camp->items->contains(fn($ci) => (string)$ci->product_id === (string)$item['product_id'] && $ci->role === 'GET');
                    // fallback: if campaign has no GET items, treat items table with role BUY as target for BULK
                    if (!$isTarget && $camp->items->isEmpty()) continue;
                    if (!$isTarget && $camp->items->where('role', 'GET')->isEmpty()) {
                        $isTarget = $camp->items->contains(fn($ci) => (string)$ci->product_id === (string)$item['product_id']);
                    }
                    if (!$isTarget) continue;

                    $amount = 0;
                    if ($reward->reward_type === 'PERCENT_DISCOUNT') {
                        $amount = round(($item['price'] * ($reward->reward_value / 100)) * $item['qty']);
                    } elseif ($reward->reward_type === 'FIXED_DISCOUNT') {
                        $amount = $reward->reward_value * $item['qty'];
                    } elseif ($reward->reward_type === 'FREE_PRODUCT') {
                        $amount = $reward->reward_value * $item['qty']; // value as qty free * price
                    }
                    if ($amount > 0) {
                        $totalDiscount += $amount;
                        $details[] = ['name' => $camp->name . ' (' . $item['name'] . ')', 'amount' => $amount];
                    }
                }
            } elseif (in_array($camp->type, ['BUNDLE', 'BUY_X_GET_Y'])) {
                $requirements = $camp->items->where('role', 'BUY');
                if ($requirements->isEmpty()) $requirements = $camp->items;

                $maxSets = PHP_INT_MAX;
                $met = true;
                foreach ($requirements as $req) {
                    $available = 0;
                    foreach ($cartItems as $it) {
                        if ((string)$it['product_id'] === (string)$req->product_id) {
                            $available += $it['qty'] - ($usedQty[$it['product_id']] ?? 0);
                        }
                    }
                    if ($available < $req->quantity) { $met = false; break; }
                    $setsForThis = intdiv($available, max(1, $req->quantity));
                    $maxSets = min($maxSets, $setsForThis);
                }
                if (!$met || $maxSets <= 0 || $maxSets === PHP_INT_MAX) continue;

                $rewardAmount = 0;
                if ($reward->reward_type === 'FIXED_DISCOUNT') {
                    $rewardAmount = $reward->reward_value * $maxSets;
                } elseif ($reward->reward_type === 'PERCENT_DISCOUNT') {
                    $reqTotal = 0;
                    foreach ($requirements as $req) {
                        foreach ($cartItems as $it) {
                            if ((string)$it['product_id'] === (string)$req->product_id) {
                                $reqTotal += $it['price'] * $req->quantity;
                                break;
                            }
                        }
                    }
                    $rewardAmount = round(($reqTotal * ($reward->reward_value / 100)) * $maxSets);
                } elseif ($reward->reward_type === 'FREE_PRODUCT') {
                    // treat as fixed discount fallback
                    $rewardAmount = $reward->reward_value * $maxSets;
                }

                if ($rewardAmount > 0) {
                    $totalDiscount += $rewardAmount;
                    $details[] = ['name' => $camp->name, 'amount' => $rewardAmount];
                    foreach ($requirements as $req) {
                        $needed = $req->quantity * $maxSets;
                        foreach ($cartItems as $it) {
                            if ((string)$it['product_id'] !== (string)$req->product_id) continue;
                            if ($needed <= 0) break;
                            $available = $it['qty'] - ($usedQty[$it['product_id']] ?? 0);
                            $consume = min($available, $needed);
                            $usedQty[$it['product_id']] = ($usedQty[$it['product_id']] ?? 0) + $consume;
                            $needed -= $consume;
                        }
                    }
                }
            }
        }

        return [
            'total' => $totalDiscount,
            'details' => $details,
            'note' => collect($details)->pluck('name')->implode(', '),
        ];
    }
}

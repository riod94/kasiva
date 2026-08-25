<?php

namespace App\Services;

use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use Illuminate\Support\Facades\Log;

/**
 * Wrapper khusus cart → loyalty agar CashierScreen & CheckoutService tidak god-object.
 * Membungkus LoyaltyService (port Ngepos) dengan context keranjang checkout.
 */
class LoyaltyCartService
{
    /**
     * Post-checkout: tambah stamp jika eligible + claim reward jika ada.
     *
     * @return LoyaltyProgram|null program yang dipakai untuk flash
     */
    public function handlePostCheckout(
        ?string $linkedMemberId,
        ?string $appliedRewardId,
        float $subtotalAmount,
        float $discountTotal,
        array $cartSnapshot,
        string $transactionId,
    ): ?LoyaltyProgram {
        if (! $linkedMemberId) {
            // tetap claim reward tanpa stamp? tidak — reward butuh member
            if ($appliedRewardId) {
                try {
                    LoyaltyService::claimReward($appliedRewardId, $transactionId);
                } catch (\Throwable $e) {
                    Log::warning('Kasiva loyalty claimReward tanpa member gagal', ['error' => $e->getMessage()]);
                }
            }

            return LoyaltyProgram::where('is_active', true)->first();
        }

        $lp = LoyaltyProgram::where('is_active', true)->first();

        try {
            if ($lp) {
                $cartProductIds = array_map(fn ($it) => (string) ($it['id'] ?? ''), array_values($cartSnapshot));
                $eligible = LoyaltyService::isStampEligible($subtotalAmount, $discountTotal > 0, $cartProductIds, $lp);
                if ($eligible) {
                    $member = LoyaltyMember::find($linkedMemberId);
                    if ($member) {
                        LoyaltyService::addStamp($member, $lp, $transactionId);
                        LoyaltyService::checkAndCreateReward($member, $lp);
                    }
                }
            }
            if ($appliedRewardId) {
                LoyaltyService::claimReward($appliedRewardId, $transactionId);
            }
        } catch (\Throwable $e) {
            Log::warning('Kasiva loyalty post-checkout gagal', ['error' => $e->getMessage(), 'transaction_id' => $transactionId]);
        }

        return $lp;
    }

    /**
     * Bangun progress member untuk banner & flash.
     *
     * @return array{currentStamps:int,targetStamps:int,isEligibleForReward:bool,oldestStampDate:?object,expiresAt:?object}|null
     */
    public function progressFor(?LoyaltyMember $member, ?LoyaltyProgram $program): ?array
    {
        if (! $member || ! $program) {
            return null;
        }

        try {
            return LoyaltyService::getCustomerProgress($member, $program);
        } catch (\Throwable $e) {
            Log::warning('Kasiva loyalty progress gagal', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function resolveMember(string $raw): ?LoyaltyMember
    {
        $id = LoyaltyService::parseQrCode($raw);
        if ($id) {
            return LoyaltyMember::where('qr_code', 'like', '%'.$id)->orWhere('id', $id)->first();
        }

        // fallback: cari by phone
        $clean = preg_replace('/\D+/', '', $raw);

        return $clean ? LoyaltyMember::where('phone', $clean)->first() : null;
    }
}

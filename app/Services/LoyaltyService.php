<?php

namespace App\Services;

use App\Models\CustomerReward;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyStamp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Port dari Ngepos src/stores/loyalty.ts
 */
class LoyaltyService
{
    public static function formatQrCode(string $id): string
    {
        return 'KSV-MBR-'.$id;
    }

    /**
     * Parse QR mentah -> id member.
     * Support: KSV-MBR-xxx, NGEPOS-MBR-xxx, URL /m/KSV-MBR-xxx, /m/NGEPOS-MBR-xxx
     */
    public static function parseQrCode(string $raw): ?string
    {
        $raw = trim($raw);
        if (str_contains($raw, '/m/KSV-MBR-')) {
            $part = explode('/m/KSV-MBR-', $raw)[1] ?? null;

            return $part ? explode('/', $part)[0] : null;
        }
        if (str_contains($raw, '/m/NGEPOS-MBR-')) {
            $part = explode('/m/NGEPOS-MBR-', $raw)[1] ?? null;

            return $part ? explode('/', $part)[0] : null;
        }
        if (str_contains($raw, 'KSV-MBR-')) {
            return explode('KSV-MBR-', $raw)[1] ?? null;
        }
        if (str_contains($raw, 'NGEPOS-MBR-')) {
            return explode('NGEPOS-MBR-', $raw)[1] ?? null;
        }

        return null;
    }

    public static function isStampEligible(float $transactionTotal, bool $discountApplied, array $cartProductIds, LoyaltyProgram $program): bool
    {
        if ($transactionTotal < (float) ($program->min_transaction ?? 0)) {
            return false;
        }
        if ($discountApplied && ! (bool) ($program->allow_with_promo ?? false)) {
            return false;
        }
        $excluded = $program->excluded_product_ids ?? [];
        if (! empty($excluded)) {
            $hasValid = false;
            foreach ($cartProductIds as $pid) {
                if (! in_array($pid, $excluded, true)) {
                    $hasValid = true;
                    break;
                }
            }
            if (! $hasValid) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{currentStamps:int,targetStamps:int,isEligibleForReward:bool,oldestStampDate:?Carbon,expiresAt:?Carbon}
     */
    public static function getCustomerProgress(LoyaltyMember $member, LoyaltyProgram $program): array
    {
        $expiryMonths = (int) ($program->expiry_months ?? 12);
        $target = (int) ($program->target_stamps ?? 10);
        $threshold = now()->subMonths($expiryMonths);

        $validStamps = $member->stamps()
            ->where(function ($q) use ($threshold) {
                $q->where('stamped_at', '>', $threshold)
                    ->orWhere(function ($q2) use ($threshold) {
                        $q2->whereNull('stamped_at')->where('created_at', '>', $threshold);
                    });
            })
            ->when($program->id, fn ($q) => $q->where(function ($qq) use ($program) {
                $qq->where('program_id', $program->id)->orWhereNull('program_id');
            }))
            ->orderBy('stamped_at')
            ->orderBy('created_at')
            ->get();

        // jika program_id filter menghilangkan semua karena null, fallback hitung semua valid
        if ($validStamps->isEmpty()) {
            $validStamps = $member->stamps()
                ->where(function ($q) use ($threshold) {
                    $q->where('stamped_at', '>', $threshold)
                        ->orWhere(function ($q2) use ($threshold) {
                            $q2->whereNull('stamped_at')->where('created_at', '>', $threshold);
                        });
                })
                ->orderBy('stamped_at')->get();
        }

        $current = $validStamps->count();
        $oldest = $validStamps->first();
        $oldestDate = $oldest ? Carbon::parse($oldest->stamped_at ?? $oldest->created_at) : null;
        $expiresAt = $oldestDate ? (clone $oldestDate)->addMonths($expiryMonths) : null;

        return [
            'currentStamps' => $current,
            'targetStamps' => $target,
            'isEligibleForReward' => $current >= $target,
            'oldestStampDate' => $oldestDate,
            'expiresAt' => $expiresAt,
        ];
    }

    public static function addStamp(LoyaltyMember $member, LoyaltyProgram $program, ?string $transactionId = null): LoyaltyStamp
    {
        $stamp = LoyaltyStamp::create([
            'loyalty_member_id' => $member->id,
            'program_id' => $program->id,
            'transaction_id' => $transactionId,
            'stamps_earned' => 1,
            'stamped_at' => now(),
        ]);
        $member->increment('stamps_count');
        $member->increment('total_visits');

        return $stamp;
    }

    public static function checkAndCreateReward(LoyaltyMember $member, LoyaltyProgram $program): ?CustomerReward
    {
        $progress = self::getCustomerProgress($member, $program);
        if (! $progress['isEligibleForReward']) {
            return null;
        }

        // jangan duplikasi AVAILABLE yang belum expired
        $existingAvailable = CustomerReward::where('loyalty_member_id', $member->id)
            ->where('program_id', $program->id)
            ->where('status', 'AVAILABLE')
            ->where('expires_at', '>', now())
            ->exists();
        if ($existingAvailable) {
            return null;
        }

        return CustomerReward::create([
            'loyalty_member_id' => $member->id,
            'program_id' => $program->id,
            'status' => 'AVAILABLE',
            'available_at' => now(),
            'expires_at' => now()->addDays((int) ($program->reward_claim_days ?? 30)),
        ]);
    }

    public static function claimReward(string $rewardId, string $transactionId): void
    {
        $reward = CustomerReward::findOrFail($rewardId);
        $reward->update([
            'status' => 'CLAIMED',
            'claimed_at' => now(),
            'claimed_transaction_id' => $transactionId,
        ]);
        $program = LoyaltyProgram::find($reward->program_id);
        if ($program && ($program->after_claim ?? 'RESET') === 'RESET') {
            // hapus semua stamps valid untuk member/program ini (reset)
            LoyaltyStamp::where('loyalty_member_id', $reward->loyalty_member_id)
                ->where(function ($q) use ($program) {
                    $q->where('program_id', $program->id)->orWhereNull('program_id');
                })->delete();
            LoyaltyMember::where('id', $reward->loyalty_member_id)->update(['stamps_count' => 0]);
        } else {
            // COMPLETE: kurangi targetStamps saja — hapus oldest N stamps
            $target = (int) ($program->target_stamps ?? 10);
            $ids = LoyaltyStamp::where('loyalty_member_id', $reward->loyalty_member_id)
                ->orderBy('stamped_at')->orderBy('created_at')->limit($target)->pluck('id');
            if ($ids->isNotEmpty()) {
                LoyaltyStamp::whereIn('id', $ids)->delete();
                $member = LoyaltyMember::find($reward->loyalty_member_id);
                if ($member) {
                    $member->decrement('stamps_count', min($target, (int) $member->stamps_count));
                }
            }
        }
    }

    /** @return Collection<int,CustomerReward> */
    public static function getAvailableRewards(LoyaltyMember $member)
    {
        return CustomerReward::where('loyalty_member_id', $member->id)
            ->where('status', 'AVAILABLE')
            ->where('expires_at', '>', now())
            ->orderBy('available_at')
            ->get();
    }

    public static function resetStamps(LoyaltyMember $member, LoyaltyProgram $program): void
    {
        LoyaltyStamp::where('loyalty_member_id', $member->id)
            ->where(function ($q) use ($program) {
                $q->where('program_id', $program->id)->orWhereNull('program_id');
            })->delete();
        $member->update(['stamps_count' => 0]);
    }
}

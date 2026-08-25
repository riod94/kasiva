<?php

use App\Models\CustomerReward;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyStamp;
use App\Models\Transaction;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('member qr format KSV-MBR- dan unik', function () {
    $m1 = LoyaltyMember::create(['name' => 'QR A', 'phone' => '081111111111', 'stamps_count' => 0, 'total_visits' => 0]);
    $m2 = LoyaltyMember::create(['name' => 'QR B', 'phone' => '081222222222', 'stamps_count' => 0, 'total_visits' => 0]);
    expect($m1->qr_code)->toStartWith('KSV-MBR-');
    expect($m2->qr_code)->toStartWith('KSV-MBR-');
    expect($m1->qr_code)->not->toBe($m2->qr_code);
    expect(strlen($m1->qr_code))->toBeGreaterThan(12);
});

test('parse qr support KSV, NGEPOS, url /m/', function () {
    expect(LoyaltyService::parseQrCode('KSV-MBR-ABC12345'))->toBe('ABC12345');
    expect(LoyaltyService::parseQrCode('NGEPOS-MBR-XYZ999'))->toBe('XYZ999');
    expect(LoyaltyService::parseQrCode('https://kasiva.biz.id/m/KSV-MBR-HELLO123'))->toBe('HELLO123');
    expect(LoyaltyService::parseQrCode('https://kasiva.biz.id/m/NGEPOS-MBR-HELLO123'))->toBe('HELLO123');
    expect(LoyaltyService::parseQrCode('081234567890'))->toBeNull();
});

test('is stamp eligible memeriksa minTransaction, allowWithPromo, excludedProductIds', function () {
    $prog = LoyaltyProgram::create([
        'name' => 'Test Program',
        'target_stamps' => 10,
        'min_transaction' => 15000,
        'expiry_months' => 6,
        'reward_type' => 'DISCOUNT',
        'is_active' => true,
        'allow_with_promo' => false,
        'excluded_product_ids' => ['prod-excluded'],
    ]);
    expect(LoyaltyService::isStampEligible(10000, false, ['prod-1'], $prog))->toBeFalse(); // below min
    expect(LoyaltyService::isStampEligible(20000, true, ['prod-1'], $prog))->toBeFalse(); // promo not allowed
    expect(LoyaltyService::isStampEligible(20000, false, ['prod-excluded'], $prog))->toBeFalse(); // only excluded
    expect(LoyaltyService::isStampEligible(20000, false, ['prod-1', 'prod-excluded'], $prog))->toBeTrue();
    // allow_with_promo true
    $prog->update(['allow_with_promo' => true]);
    expect(LoyaltyService::isStampEligible(20000, true, ['prod-1'], $prog->fresh()))->toBeTrue();
});

test('customer progress mem-filter expiry per stamp', function () {
    $prog = LoyaltyProgram::create([
        'name' => 'Expiry Prog', 'target_stamps' => 5, 'min_transaction' => 0, 'expiry_months' => 2,
        'is_active' => true,
    ]);
    $member = LoyaltyMember::create(['name' => 'Expiry Member', 'phone' => '081333333333']);
    // stamp expired 3 months ago
    LoyaltyStamp::create(['loyalty_member_id' => $member->id, 'program_id' => $prog->id, 'stamped_at' => now()->subMonths(3), 'stamps_earned' => 1]);
    // 2 valid stamps
    LoyaltyStamp::create(['loyalty_member_id' => $member->id, 'program_id' => $prog->id, 'stamped_at' => now()->subDays(10), 'stamps_earned' => 1]);
    LoyaltyStamp::create(['loyalty_member_id' => $member->id, 'program_id' => $prog->id, 'stamped_at' => now()->subDays(5), 'stamps_earned' => 1]);

    $progress = LoyaltyService::getCustomerProgress($member, $prog);
    expect($progress['currentStamps'])->toBe(2);
    expect($progress['targetStamps'])->toBe(5);
    expect($progress['isEligibleForReward'])->toBeFalse();
    expect($progress['expiresAt'])->not->toBeNull();
});

test('add stamp dan reward lifecycle RESET', function () {
    $prog = LoyaltyProgram::create([
        'name' => 'Reward Prog', 'target_stamps' => 3, 'min_transaction' => 0, 'expiry_months' => 12,
        'reward_type' => 'DISCOUNT', 'reward_claim_days' => 7, 'after_claim' => 'RESET', 'is_active' => true,
    ]);
    $member = LoyaltyMember::create(['name' => 'Reward Member', 'phone' => '081444444444']);
    // add 3 stamps
    LoyaltyService::addStamp($member, $prog);
    LoyaltyService::addStamp($member, $prog);
    LoyaltyService::addStamp($member, $prog);
    $progress = LoyaltyService::getCustomerProgress($member->fresh(), $prog);
    expect($progress['isEligibleForReward'])->toBeTrue();

    $reward = LoyaltyService::checkAndCreateReward($member, $prog);
    expect($reward)->not->toBeNull();
    expect($reward->status)->toBe('AVAILABLE');
    // duplicate should not create second
    $again = LoyaltyService::checkAndCreateReward($member, $prog);
    expect($again)->toBeNull();

    $tx = Transaction::create(['receipt_number' => 'KSV-TEST-'.Str::upper(Str::random(4)), 'payment_method' => 'CASH', 'total_amount' => 10000, 'total_hpp' => 3000, 'paid_amount' => 10000, 'change_amount' => 0, 'cashier_name' => 'Tester', 'sync_status' => 'SYNCED']);
    LoyaltyService::claimReward($reward->id, $tx->id);
    expect(CustomerReward::find($reward->id)->status)->toBe('CLAIMED');
    // RESET should clear stamps
    expect(LoyaltyStamp::where('loyalty_member_id', $member->id)->count())->toBe(0);
    expect($member->fresh()->stamps_count)->toBe(0);
});

test('claim reward COMPLETE hanya hapus target stamps', function () {
    $prog = LoyaltyProgram::create([
        'name' => 'Complete Prog', 'target_stamps' => 2, 'min_transaction' => 0, 'expiry_months' => 12,
        'reward_type' => 'DISCOUNT', 'reward_claim_days' => 30, 'after_claim' => 'COMPLETE', 'is_active' => true,
    ]);
    $member = LoyaltyMember::create(['name' => 'Complete Member', 'phone' => '081555555555']);
    LoyaltyService::addStamp($member, $prog);
    LoyaltyService::addStamp($member, $prog);
    LoyaltyService::addStamp($member, $prog); // 3 stamps, target 2
    $reward = LoyaltyService::checkAndCreateReward($member, $prog);
    $tx = Transaction::create(['receipt_number' => 'KSV-TEST-'.Str::upper(Str::random(4)), 'payment_method' => 'CASH', 'total_amount' => 10000, 'total_hpp' => 3000, 'paid_amount' => 10000, 'change_amount' => 0, 'cashier_name' => 'Tester', 'sync_status' => 'SYNCED']);
    LoyaltyService::claimReward($reward->id, $tx->id);
    // should leave 1 stamp remaining (3-2)
    expect(LoyaltyStamp::where('loyalty_member_id', $member->id)->count())->toBe(1);
});

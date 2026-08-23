<?php

use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyStamp;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\LoyaltyService;
use Livewire\Livewire;
use App\Livewire\Pos\CashierScreen;
use App\Livewire\Marketing\MemberManager;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('cashier dapat link member via qr dan menampilkan progress', function () {
    $user = User::factory()->create();
    $member = LoyaltyMember::create(['name'=>'Member Kasir','phone'=>'081666666666']);
    $prog = LoyaltyProgram::create([
        'name'=>'Stamp 5 Gratis 1','target_stamps'=>5,'min_transaction'=>0,'expiry_months'=>12,'is_active'=>true,
    ]);

    $comp = Livewire::actingAs($user)->test(CashierScreen::class);
    $comp->call('linkMemberByQr', $member->qr_code);
    $comp->assertSet('linkedMemberId', $member->id);
    // progress should be 0/5
    $progress = $comp->get('memberProgress');
    expect($progress['currentStamps'])->toBe(0);
    expect($progress['targetStamps'])->toBe(5);
});

test('checkout dengan member tertaut menambah 1 stamp dan mengaitkan transaksi', function () {
    $user = User::factory()->create();
    $member = LoyaltyMember::create(['name'=>'Checkout Member','phone'=>'081777777777']);
    $prog = LoyaltyProgram::create([
        'name'=>'Checkout Prog','target_stamps'=>10,'min_transaction'=>0,'expiry_months'=>12,'is_active'=>true,
    ]);
    $product = Product::create(['name'=>'Kopi Test','sku'=>'KSV-LOY-001','price'=>15000,'hpp'=>5000,'current_stock'=>100,'is_active'=>true]);

    $comp = Livewire::actingAs($user)->test(CashierScreen::class);
    $comp->call('linkMemberByQr', $member->qr_code);
    $comp->call('addToCart', $product->id);
    $comp->call('openCheckoutModal');
    $comp->call('selectCashMethod');
    $comp->call('processCheckout', app(\App\Services\HppCalculatorService::class));

    $tx = Transaction::latest()->first();
    expect($tx)->not->toBeNull();
    expect($tx->loyalty_member_id)->toBe($member->id);
    expect(LoyaltyStamp::where('loyalty_member_id',$member->id)->count())->toBe(1);
});

test('checkout dengan reward terpasang akan claim dan menambah baris GIFT', function () {
    $user = User::factory()->create();
    $product = Product::create(['name'=>'Es Teh','sku'=>'KSV-GIFT-001','price'=>5000,'hpp'=>1500,'current_stock'=>100,'is_active'=>true]);
    $giftProduct = Product::create(['name'=>'Hadiah Gratis','sku'=>'KSV-GIFT-P','price'=>8000,'hpp'=>3000,'current_stock'=>50,'is_active'=>true]);
    $prog = LoyaltyProgram::create([
        'name'=>'Gift Prog','target_stamps'=>2,'min_transaction'=>0,'expiry_months'=>12,
        'reward_type'=>'FREE_PRODUCT','reward_product_id'=>$giftProduct->id,'reward_claim_days'=>30,'after_claim'=>'RESET','is_active'=>true,
    ]);
    $member = LoyaltyMember::create(['name'=>'Gift Member','phone'=>'081888888888']);
    // manually create 2 stamps to be eligible
    LoyaltyService::addStamp($member, $prog);
    LoyaltyService::addStamp($member, $prog);
    $reward = LoyaltyService::checkAndCreateReward($member, $prog);
    expect($reward)->not->toBeNull();

    $comp = Livewire::actingAs($user)->test(CashierScreen::class);
    $comp->call('linkMemberByQr', $member->qr_code);
    // refresh to load availableRewards
    $comp->call('applyReward', $reward->id);
    $comp->call('addToCart', $product->id);
    $comp->call('openCheckoutModal');
    $comp->call('selectCashMethod');
    $comp->call('processCheckout', app(\App\Services\HppCalculatorService::class));

    $tx = Transaction::latest()->first();
    expect($tx->items()->where('product_name','like','%[GIFT]%')->exists())->toBeTrue();
    expect(\App\Models\CustomerReward::find($reward->id)->status)->toBe('CLAIMED');
});

test('multi-cart persist member per slot', function () {
    $user = User::factory()->create();
    $m1 = LoyaltyMember::create(['name'=>'M1','phone'=>'081999000001']);
    $m2 = LoyaltyMember::create(['name'=>'M2','phone'=>'081999000002']);
    $prog = LoyaltyProgram::create(['name'=>'Persist Prog','target_stamps'=>10,'min_transaction'=>0,'expiry_months'=>12,'is_active'=>true]);

    $comp = Livewire::actingAs($user)->test(CashierScreen::class);
    $comp->call('linkMemberByQr', $m1->qr_code);
    expect($comp->get('linkedMemberId'))->toBe($m1->id);

    $comp->call('createNewCart'); // cart 2
    expect($comp->get('linkedMemberId'))->toBeNull(); // new cart empty

    $comp->call('linkMemberByQr', $m2->qr_code);
    expect($comp->get('linkedMemberId'))->toBe($m2->id);

    $comp->call('switchCart', 0);
    expect($comp->get('linkedMemberId'))->toBe($m1->id);

    $comp->call('switchCart', 1);
    expect($comp->get('linkedMemberId'))->toBe($m2->id);
});

test('member manager batch generate dan filter tab', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $comp = Livewire::test(MemberManager::class);
    $comp->set('batchCount', 5)->call('generateBatch');
    expect(LoyaltyMember::where('status','UNASSIGNED')->count())->toBe(5);

    // create assigned
    LoyaltyMember::create(['name'=>'Assigned One','phone'=>'081123123123','status'=>'ASSIGNED']);
    $comp2 = Livewire::test(MemberManager::class);
    $comp2->call('setTab','ASSIGNED');
    $comp2->assertViewHas('members', function($paginator){
        return $paginator->total() >= 1;
    });
    $comp2->call('setTab','UNASSIGNED');
    $comp2->assertViewHas('members', function($paginator){
        return $paginator->total() >= 5;
    });
});

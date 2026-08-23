<?php

use App\Models\Campaign;
use App\Models\CampaignItem;
use App\Models\CampaignReward;
use App\Models\Product;
use App\Models\Category;
use App\Services\CampaignDiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('campaign BULK_DISCOUNT persen dihitung per item', function () {
    $cat = Category::create(['name'=>'Minuman']);
    $prod = Product::create(['category_id'=>$cat->id,'name'=>'Kopi','sku'=>'KPI-001','price'=>20000,'hpp'=>8000,'current_stock'=>100,'is_active'=>true]);
    $camp = Campaign::create(['name'=>'Diskon Kopi 10%','type'=>'BULK_DISCOUNT','is_active'=>true,'priority'=>10,'reward_type'=>'PERCENT_DISCOUNT','reward_value'=>10]);
    $camp->items()->create(['product_id'=>$prod->id,'quantity'=>1,'role'=>'GET']);
    $camp->rewards()->create(['reward_type'=>'PERCENT_DISCOUNT','reward_value'=>10]);

    $svc = new CampaignDiscountService();
    $res = $svc->calculate([['product_id'=>$prod->id,'price'=>20000,'qty'=>2,'name'=>'Kopi']]);
    expect($res['total'])->toBe(4000.0); // 10% * 20000 *2
    expect($res['details'])->toHaveCount(1);
});

test('campaign BUNDLE fixed discount per set', function () {
    $cat = Category::create(['name'=>'Makanan']);
    $p1 = Product::create(['category_id'=>$cat->id,'name'=>'Ayam','sku'=>'AY-001','price'=>25000,'hpp'=>10000,'current_stock'=>100,'is_active'=>true]);
    $p2 = Product::create(['category_id'=>$cat->id,'name'=>'Nasi','sku'=>'NS-001','price'=>10000,'hpp'=>3000,'current_stock'=>100,'is_active'=>true]);
    $camp = Campaign::create(['name'=>'Paket Hemat','type'=>'BUNDLE','is_active'=>true,'priority'=>5,'reward_type'=>'FIXED_DISCOUNT','reward_value'=>5000]);
    $camp->items()->create(['product_id'=>$p1->id,'quantity'=>1,'role'=>'BUY']);
    $camp->items()->create(['product_id'=>$p2->id,'quantity'=>1,'role'=>'BUY']);
    $camp->rewards()->create(['reward_type'=>'FIXED_DISCOUNT','reward_value'=>5000]);

    $svc = new CampaignDiscountService();
    $res = $svc->calculate([
        ['product_id'=>$p1->id,'price'=>25000,'qty'=>2,'name'=>'Ayam'],
        ['product_id'=>$p2->id,'price'=>10000,'qty'=>2,'name'=>'Nasi'],
    ]);
    expect($res['total'])->toBe(10000.0); // 2 sets * 5000
});

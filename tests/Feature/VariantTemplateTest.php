<?php

use App\Livewire\Inventory\VariationManager;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VariantTemplate;
use App\Models\VariantTemplateOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('variant template dapat dibuat dan dipakai untuk autopopulate varian produk', function () {
    $user = User::factory()->create();
    $tpl = VariantTemplate::create(['name' => 'Level Pedas', 'selection_type' => 'SINGLE', 'is_required' => true]);
    VariantTemplateOption::create(['variant_template_id' => $tpl->id, 'name' => 'Tidak Pedas', 'price_modifier' => 0, 'cogs_modifier' => 0]);
    VariantTemplateOption::create(['variant_template_id' => $tpl->id, 'name' => 'Pedas', 'price_modifier' => 1000, 'cogs_modifier' => 300]);
    $prod = Product::create(['name' => 'Ayam Geprek Kasiva', 'sku' => 'KSV-GEPREK-001', 'price' => 18000, 'hpp' => 7000, 'current_stock' => 10]);
    $this->actingAs($user);
    $comp = Livewire::test(VariationManager::class)->call('useTemplate', $tpl->id);
    expect($comp->get('name'))->toBe('Level Pedas');
    expect($comp->get('options'))->toHaveCount(2);
    $comp->set('productId', $prod->id)->call('saveVariant')->assertHasNoErrors();
    expect(ProductVariant::where('name', 'Level Pedas')->exists())->toBeTrue();
});

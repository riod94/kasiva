<?php

use App\Livewire\Marketing\LoyaltyManager;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('loyalty member memiliki qr_code accessor unik', function () {
    $m = LoyaltyMember::create(['name' => 'QR Tester', 'phone' => '081234567890', 'stamps_count' => 3, 'total_visits' => 5]);
    expect($m->qr_code)->toStartWith('KSV-MBR-');
    expect(strlen($m->qr_code))->toBeGreaterThan(12);
    $m2 = LoyaltyMember::create(['name' => 'QR Tester 2', 'phone' => '081299999999', 'stamps_count' => 0, 'total_visits' => 0]);
    expect($m->qr_code)->not->toBe($m2->qr_code);
});

test('loyalty program konfiguratif dapat dibuat dan dirender di manager', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $comp = Livewire::test(LoyaltyManager::class)
        ->set('programName', 'Stamp 8 Gratis 1')
        ->set('targetStamps', 8)
        ->set('minTransaction', 15000)
        ->set('expiryMonths', 6)
        ->call('saveProgram')
        ->assertHasNoErrors();
    expect(LoyaltyProgram::where('name', 'Stamp 8 Gratis 1')->exists())->toBeTrue();
    $pr = LoyaltyProgram::first();
    expect($pr->target_stamps)->toBe(8);
    expect($pr->expiry_months)->toBe(6);
});

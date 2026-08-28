<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('halaman landing page dapat diakses dengan sukses', function () {
    $this->get('/')
        ->assertStatus(200)
        ->assertSee('Kasiva menjaga profit');
});

test('landing page memiliki metadata SEO dan schema terstruktur', function () {
    $response = $this->get('/')->assertOk();

    $response
        ->assertSee('<title>Kasiva POS — Aplikasi Kasir, HPP, Stok &amp; Laporan Profit F&amp;B</title>', false)
        ->assertSee('name="description"', false)
        ->assertSee('rel="canonical"', false)
        ->assertSee('property="og:image"', false)
        ->assertSee('name="twitter:card" content="summary_large_image"', false);

    preg_match_all('/<script[^>]*type="application\/ld\+json"[^>]*>(.*?)<\/script>/s', $response->getContent(), $matches);
    $schemas = array_map(fn (string $json): array => json_decode($json, true, 512, JSON_THROW_ON_ERROR), $matches[1]);

    expect(array_column($schemas, '@type'))->toContain('Organization', 'SoftwareApplication', 'FAQPage');
    expect(array_unique(array_column($schemas, '@context')))->toBe(['https://schema.org']);
});

test('landing page tidak memuat runtime aplikasi POS yang tidak diperlukan', function () {
    $response = $this->get('/')->assertOk();

    $response
        ->assertDontSee('resources/js/app.js', false);
});

test('social preview landing tersedia dalam format PNG', function () {
    $path = public_path('images/kasiva-social-preview.png');

    expect($path)->toBeFile();
    expect(getimagesize($path))->toMatchArray([0 => 1200, 1 => 630, 'mime' => 'image/png']);
});

test('halaman login dan register dapat diakses dengan sukses', function () {
    $this->get('/login')->assertStatus(200);
    $this->get('/register')->assertStatus(200);
});

test('pengguna dapat melakukan registrasi outlet baru', function () {
    Livewire::test(Register::class)
        ->set('outlet_name', 'Kedai Kopi Kasiva Solo')
        ->set('name', 'Rizki Owner')
        ->set('email', 'rizki@kasiva.pos')
        ->set('phone', '08123456789')
        ->set('password', 'password123')
        ->call('register')
        ->assertRedirect(route('pos.cashier'));

    expect(User::count())->toBe(1);
    expect(User::first()->email)->toBe('rizki@kasiva.pos');
});

test('registrasi menghasilkan PIN acak 6 digit (bukan hardcode 123456)', function () {
    Livewire::test(Register::class)
        ->set('outlet_name', 'Outlet A')
        ->set('name', 'Owner A')
        ->set('email', 'a@kasiva.pos')
        ->set('password', 'password123')
        ->call('register');

    $user = User::first();
    expect($user->pin)->not->toBeNull();
    expect($user->pin)->not->toBe(Hash::make('123456'));
    expect($user->must_change_pin)->toBeTrue();

    $flash = session('initial_pin');
    expect($flash)->toBeString();
    expect($flash)->toMatch('/^\d{6}$/');
});

test('dua registrasi menghasilkan PIN yang berbeda (keacakan terjaga)', function () {
    Livewire::test(Register::class)
        ->set('outlet_name', 'Outlet A')->set('name', 'A')->set('email', 'a@kasiva.pos')->set('password', 'password123')
        ->call('register');
    $pin1 = session('initial_pin');

    Auth::logout();
    Livewire::test(Register::class)
        ->set('outlet_name', 'Outlet B')->set('name', 'B')->set('email', 'b@kasiva.pos')->set('password', 'password123')
        ->call('register');
    $pin2 = session('initial_pin');

    expect($pin1)->toMatch('/^\d{6}$/');
    expect($pin2)->toMatch('/^\d{6}$/');
    expect($pin1)->not->toBe($pin2);
});

test('pengguna dapat melakukan login dengan kredensial yang valid', function () {
    $user = User::create([
        'name' => 'Staf Kasir',
        'email' => 'staf@kasiva.pos',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('email', 'staf@kasiva.pos')
        ->set('password', 'password123')
        ->call('login')
        ->assertRedirect(route('pos.cashier'));

    $this->assertAuthenticatedAs($user);
});

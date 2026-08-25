<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('halaman landing page dapat diakses dengan sukses', function () {
    $this->get('/')
        ->assertStatus(200)
        ->assertSee('Kendali Penuh Profit');
});

test('halaman login dan register dapat diakses dengan sukses', function () {
    $this->get('/login')->assertStatus(200)->assertSee('Masuk Akun Kasiva');
    $this->get('/register')->assertStatus(200)->assertSee('Pendaftaran Akun Outlet');
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

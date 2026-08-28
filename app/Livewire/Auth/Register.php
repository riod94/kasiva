<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Register extends Component
{
    public $outlet_name = '';

    public $name = '';

    public $email = '';

    public $password = '';

    public $phone = '';

    public function register()
    {
        $this->validate([
            'outlet_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        $throttleKey = 'register|'.Str::lower(request()->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', 'Terlalu banyak percobaan registrasi. Coba lagi dalam '.$seconds.' detik.');

            return;
        }

        $outlet = Outlet::create([
            'name' => $this->outlet_name,
            'phone' => $this->phone ?? '',
            'tax_percentage' => 10.0,
            'service_charge_percentage' => 5.0,
        ]);

        $ownerRole = Role::firstOrCreate(
            ['slug' => 'owner'],
            ['name' => 'Owner / Pemilik', 'description' => 'Akses penuh seluruh modul']
        );

        $plainPin = (string) random_int(100000, 999999);
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone' => $this->phone,
            'role_id' => $ownerRole->id,
            'outlet_id' => $outlet->id,
            'pin' => Hash::make($plainPin),
            'must_change_password' => true,
            'must_change_pin' => true,
        ]);

        RateLimiter::clear($throttleKey);
        Auth::login($user);
        session()->regenerate();
        session()->flash('initial_pin', $plainPin);
        AuditLog::log('REGISTER', 'Registered new SaaS Outlet & Owner account', $user->name);

        return redirect()->route('pos.cashier');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.guest');
    }
}

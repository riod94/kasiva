<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Login extends Component
{
    public $email = '';

    public $password = '';

    public $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', 'Terlalu banyak percobaan login. Coba lagi dalam '.$seconds.' detik.');

            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            AuditLog::log('LOGIN', 'User logged in via Web UI', Auth::user()->name);

            $user = Auth::user();
            if ($user && ($user->must_change_password ?? false)) {
                session()->flash('warning', 'Demi keamanan, silakan ganti password default Anda segera di menu Profil.');

                return redirect()->route('profile.show');
            }

            return redirect()->route('pos.cashier');
        }

        RateLimiter::hit($throttleKey, 60);
        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}

<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserProfile extends Component
{
    public string $name = 'Owner Kasiva';

    public string $email = 'owner@kasiva.id';

    public string $phone = '081234567890';

    public string $current_pin = '';

    public string $new_pin = '';

    public string $new_pin_confirmation = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user() ?? User::first();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '081234567890';
        }
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user() ?? User::first();
        if ($user) {
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'must_change_password' => false,
            ]);
        }
        session()->flash('message', 'Profil pengguna berhasil diperbarui.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = Auth::user() ?? User::first();
        if ($user) {
            $user->update([
                'password' => Hash::make($this->password ?? request('password')),
                'must_change_password' => false,
            ]);
            // sync property if exists on component
            if (property_exists($this, 'password')) {
                $this->reset('password', 'password_confirmation');
            }
        }
        session()->flash('message', 'Password berhasil diperbarui.');
    }

    public function updatePin(): void
    {
        $this->validate([
            'new_pin' => 'required|string|digits:6|same:new_pin_confirmation',
        ]);

        $user = Auth::user() ?? User::first();
        if ($user) {
            $user->update([
                'pin' => Hash::make($this->new_pin),
                'must_change_password' => false,
            ]);
        }
        $this->reset(['current_pin', 'new_pin', 'new_pin_confirmation']);
        session()->flash('message', 'PIN 6-digit berhasil diperbarui.');
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.profile.user-profile')->layout('layouts.app');
    }
}

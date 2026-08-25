<?php

namespace App\Livewire;

use Livewire\Component;

class LandingPage extends Component
{
    public function render()
    {
        return view('livewire.landing-page')->layout('layouts.guest', [
            'title' => 'Kasiva POS — Aplikasi Kasir, HPP, Stok & Laporan Profit F&B',
            'description' => 'Kelola kasir, HPP resep, stok bahan, loyalitas, pembayaran, dan laporan profit dalam satu aplikasi POS offline-first untuk F&B dan retail Indonesia.',
            'loadAppScripts' => false,
        ]);
    }
}

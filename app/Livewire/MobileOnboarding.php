<?php

namespace App\Livewire;

use Livewire\Component;

class MobileOnboarding extends Component
{
    public $currentSlide = 0;

    public $slides = [
        [
            'title' => 'Kasir Cepat & Berfungsi Offline',
            'subtitle' => 'Transaksi kasir tanpa khawatir gangguan koneksi internet. Data lokal tersinkron otomatis ke server saat online.',
            'icon' => 'zap',
            'badge' => 'Offline-Ready',
            'color' => '#00AAA6',
        ],
        [
            'title' => 'Resep Menu & Kalkulator HPP',
            'subtitle' => 'Kalkulasi otomatis modal bahan baku per porsi dengan indikator margin 4-tier dari kritis hingga optimal.',
            'icon' => 'chart-bar',
            'badge' => 'Manajemen Modal',
            'color' => '#3EDAD7',
        ],
        [
            'title' => 'Printer Thermal & Multi-Kanal',
            'subtitle' => 'Cetak struk ke printer Bluetooth 58mm/80mm fisik, barcode scanner, dan penyesuaian pesanan online delivery.',
            'icon' => 'printer',
            'badge' => 'Perangkat Kasir',
            'color' => '#8696ED',
        ],
    ];

    public function nextSlide()
    {
        if ($this->currentSlide < count($this->slides) - 1) {
            $this->currentSlide++;
        } else {
            return redirect()->route('login');
        }
    }

    public function setSlide($index)
    {
        $this->currentSlide = $index;
    }

    public function render()
    {
        return view('livewire.mobile-onboarding')->layout('layouts.guest');
    }
}

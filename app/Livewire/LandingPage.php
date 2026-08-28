<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Support\LandingContent;
use Livewire\Component;

class LandingPage extends Component
{
    public bool $mobileNavOpen = false;

    public function toggleMobileNav(): void
    {
        $this->mobileNavOpen = ! $this->mobileNavOpen;
    }

    public function closeMobileNav(): void
    {
        $this->mobileNavOpen = false;
    }

    public function render()
    {
        return view('livewire.landing-page', [
            'features' => LandingContent::features(),
            'howItWorks' => LandingContent::howItWorks(),
            'pricingPackages' => LandingContent::pricingPackages(),
            'platforms' => LandingContent::platforms(),
            'faqs' => LandingContent::faqs(),
            'testimonials' => LandingContent::testimonials(),
            'metrics' => LandingContent::metrics(),
        ])->layout('layouts.guest', [
            'title' => 'Kasiva POS — Aplikasi Kasir, HPP, Stok & Laporan Profit F&B',
            'description' => 'Kelola kasir, HPP resep, stok bahan, loyalitas, pembayaran, dan laporan profit dalam satu aplikasi POS offline-first untuk F&B dan retail Indonesia.',
            'loadAppScripts' => false,
        ]);
    }
}

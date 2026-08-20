<?php

namespace App\Livewire\Marketing;

use App\Models\LoyaltyMember;
use App\Models\Promotion;
use Livewire\Component;

class MarketingHub extends Component
{
    public function mount(): void
    {
        $user = auth()->user();
        abort_unless(
            $user && (
                $user->isOwner() ||
                $user->hasPermission('MANAGE_PROMOS') ||
                $user->hasPermission('VIEW_MEMBERS') ||
                $user->hasPermission('MANAGE_LOYALTY')
            ),
            403,
            'Akses Ditolak: Anda tidak memiliki izin untuk melihat modul pemasaran.'
        );
    }

    public function render()
    {
        $totalMembers = LoyaltyMember::count();
        $totalActivePromos = Promotion::where('is_active', true)->count();

        return view('livewire.marketing.marketing-hub', [
            'totalMembers' => $totalMembers,
            'totalActivePromos' => $totalActivePromos,
        ])->layout('layouts.app');
    }
}

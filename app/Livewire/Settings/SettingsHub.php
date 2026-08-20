<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class SettingsHub extends Component
{
    public function mount(): void
    {
        $user = auth()->user();
        abort_unless(
            $user && (
                $user->isOwner() ||
                $user->hasPermission('MANAGE_OUTLET') ||
                $user->hasPermission('MANAGE_STAFF') ||
                $user->hasPermission('MANAGE_ROLES') ||
                $user->hasPermission('MANAGE_PAYMENTS') ||
                $user->hasPermission('MANAGE_PRINTER')
            ),
            403,
            'Akses Ditolak: Anda tidak memiliki izin untuk mengakses pusat pengaturan sistem.'
        );
    }

    public function render()
    {
        return view('livewire.settings.settings-hub')->layout('layouts.app');
    }
}

<?php

namespace App\Livewire\Settings;

use App\Models\Outlet;
use Livewire\Component;

class OutletSettings extends Component
{
    public string $name = 'Kasiva Coffee & Kitchen';

    public string $address = 'Jl. Kemang Raya No. 45, Jakarta Selatan';

    public string $phone = '081298765432';

    public float $tax_percentage = 10.0;

    public float $service_charge_percentage = 5.0;

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_OUTLET'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola profil outlet.');

        $outlet = Outlet::first();
        if ($outlet) {
            $this->name = $outlet->name;
            $this->address = $outlet->address ?? '';
            $this->phone = $outlet->phone ?? '';
            $this->tax_percentage = (float) $outlet->tax_percentage;
            $this->service_charge_percentage = (float) $outlet->service_charge_percentage;
        }
    }

    public function saveSettings(): void
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'service_charge_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $outlet = Outlet::first();
        if ($outlet) {
            $outlet->update([
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
                'tax_percentage' => $this->tax_percentage,
                'service_charge_percentage' => $this->service_charge_percentage,
            ]);
        } else {
            Outlet::create([
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
                'tax_percentage' => $this->tax_percentage,
                'service_charge_percentage' => $this->service_charge_percentage,
            ]);
        }

        session()->flash('message', 'Profil outlet dan pengaturan pajak berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.settings.outlet-settings')->layout('layouts.app');
    }
}

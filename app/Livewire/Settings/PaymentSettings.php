<?php

namespace App\Livewire\Settings;

use App\Models\PaymentSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Metode Pembayaran - Kasiva POS')]
class PaymentSettings extends Component
{
    use WithFileUploads;

    public ?string $qris_image = null;
    public $qris_file = null;
    public bool $enable_gofood = true;
    public bool $enable_grabfood = true;
    public bool $enable_shopeefood = true;

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_PAYMENTS'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola pengaturan metode pembayaran.');

        $this->qris_image = PaymentSetting::getValue('qris_image', '/images/kasiva-logo-full.png');
        $this->enable_gofood = PaymentSetting::getValue('enable_gofood', 'true') === 'true';
        $this->enable_grabfood = PaymentSetting::getValue('enable_grabfood', 'true') === 'true';
        $this->enable_shopeefood = PaymentSetting::getValue('enable_shopeefood', 'true') === 'true';
    }

    public function updatedEnableGofood($value): void
    {
        PaymentSetting::setValue('enable_gofood', $value ? 'true' : 'false');
        session()->flash('message', 'Status kanal GoFood berhasil diperbarui.');
    }

    public function updatedEnableGrabfood($value): void
    {
        PaymentSetting::setValue('enable_grabfood', $value ? 'true' : 'false');
        session()->flash('message', 'Status kanal GrabFood berhasil diperbarui.');
    }

    public function updatedEnableShopeefood($value): void
    {
        PaymentSetting::setValue('enable_shopeefood', $value ? 'true' : 'false');
        session()->flash('message', 'Status kanal ShopeeFood berhasil diperbarui.');
    }

    public function togglePlatform(string $key): void
    {
        if ($key === 'enable_gofood') {
            $this->enable_gofood = !$this->enable_gofood;
            PaymentSetting::setValue('enable_gofood', $this->enable_gofood ? 'true' : 'false');
        } elseif ($key === 'enable_grabfood') {
            $this->enable_grabfood = !$this->enable_grabfood;
            PaymentSetting::setValue('enable_grabfood', $this->enable_grabfood ? 'true' : 'false');
        } elseif ($key === 'enable_shopeefood') {
            $this->enable_shopeefood = !$this->enable_shopeefood;
            PaymentSetting::setValue('enable_shopeefood', $this->enable_shopeefood ? 'true' : 'false');
        }

        session()->flash('message', 'Status kanal penjualan berhasil diperbarui.');
    }

    public function uploadQris(): void
    {
        $this->validate([
            'qris_file' => 'required|image|max:2048',
        ]);

        $path = $this->qris_file->store('qris', 'public');
        $this->qris_image = '/storage/' . $path;
        PaymentSetting::setValue('qris_image', $this->qris_image);
        $this->qris_file = null;

        session()->flash('message', 'Kode QRIS berhasil diunggah.');
    }

    public function removeQris(): void
    {
        $this->qris_image = null;
        PaymentSetting::setValue('qris_image', '');

        session()->flash('message', 'Kode QRIS statis telah dihapus.');
    }

    public function render()
    {
        return view('livewire.settings.payment-settings');
    }
}

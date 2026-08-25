<?php

namespace App\Livewire\Settings;

use App\Models\PaymentSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pengaturan Struk - Kasiva POS')]
class ReceiptSettings extends Component
{
    public bool $showLogo = true;

    public string $footerText = "— TERIMA KASIH —\nFollow IG: @kasiva.pos";

    public string $paperWidth = '58mm';

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_PRINTER'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola pengaturan struk & printer.');

        $this->showLogo = PaymentSetting::getValue('receipt_show_logo', 'true') === 'true';
        $this->footerText = PaymentSetting::getValue('receipt_footer_text', "— TERIMA KASIH —\nFollow IG: @kasiva.pos");
        $this->paperWidth = PaymentSetting::getValue('receipt_paper_width', '58mm');
    }

    public function saveSettings(): void
    {
        PaymentSetting::setValue('receipt_show_logo', $this->showLogo ? 'true' : 'false');
        PaymentSetting::setValue('receipt_footer_text', $this->footerText);
        PaymentSetting::setValue('receipt_paper_width', $this->paperWidth);

        session()->flash('message', 'Pengaturan struk thermal berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.settings.receipt-settings');
    }
}

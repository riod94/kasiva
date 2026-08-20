<?php

namespace App\Livewire\History;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $paymentFilter = 'ALL';
    public $dateRange = 'today'; // today, 7days, month, all
    public $selectedTransaction = null;
    public $showDetailModal = false;

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('VIEW_TRANSACTIONS'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat riwayat transaksi.');
    }

    public function showDetail($transactionId)
    {
        $this->selectedTransaction = Transaction::with('items')->find($transactionId);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedTransaction = null;
    }

    public function getWhatsAppUrlProperty(): string
    {
        if (!$this->selectedTransaction) {
            return '#';
        }

        $text = "*STRUK DIGITAL KASIVA POS*\n";
        $text .= "No: " . $this->selectedTransaction->receipt_number . "\n";
        $text .= "Tanggal: " . $this->selectedTransaction->created_at->format('d/m/Y H:i') . "\n";
        $text .= "Kasir: " . $this->selectedTransaction->cashier_name . "\n";
        $text .= "--------------------------------\n";
        foreach ($this->selectedTransaction->items as $item) {
            $text .= $item->product_name . " (" . $item->quantity . "x) - Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
        }
        $text .= "--------------------------------\n";
        $text .= "TOTAL: Rp " . number_format($this->selectedTransaction->total_amount, 0, ',', '.') . "\n";
        $text .= "BAYAR (" . $this->selectedTransaction->payment_method . "): Rp " . number_format($this->selectedTransaction->paid_amount, 0, ',', '.') . "\n";
        $text .= "KEMBALI: Rp " . number_format($this->selectedTransaction->change_amount, 0, ',', '.') . "\n\n";
        $text .= "Terima kasih telah berbelanja!";

        return 'https://api.whatsapp.com/send?text=' . urlencode($text);
    }

    public function render()
    {
        $query = Transaction::with('items')->latest();

        if ($this->search) {
            $query->where('receipt_number', 'like', '%' . $this->search . '%')
                  ->orWhere('cashier_name', 'like', '%' . $this->search . '%');
        }

        if ($this->paymentFilter !== 'ALL') {
            $query->where('payment_method', $this->paymentFilter);
        }

        if ($this->dateRange === 'today') {
            $query->whereDate('created_at', now()->today());
        } elseif ($this->dateRange === '7days') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($this->dateRange === 'month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        $transactions = $query->paginate(15);

        return view('livewire.history.transaction-history', [
            'transactions' => $transactions
        ])->layout('layouts.app');
    }
}

<?php

namespace App\Livewire\History;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
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
    public $showVoidModal = false;
    public $voidTransactionId = null;
    public string $voidReason = '';

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

    public function openVoidModal(string $id): void
    {
        if (!auth()->user()->hasPermission('VOID_TRANSACTION') && !auth()->user()->isOwner()) {
            abort(403, 'Anda tidak memiliki izin VOID_TRANSACTION');
        }
        $this->voidTransactionId = $id;
        $this->voidReason = '';
        $this->showVoidModal = true;
    }

    public function closeVoidModal(): void
    {
        $this->showVoidModal = false;
        $this->voidTransactionId = null;
        $this->voidReason = '';
    }

    public function voidTransaction(): void
    {
        if (!auth()->user()->hasPermission('VOID_TRANSACTION') && !auth()->user()->isOwner()) {
            abort(403, 'Anda tidak memiliki izin VOID_TRANSACTION');
        }
        $tx = Transaction::with('items.product.materials')->findOrFail($this->voidTransactionId);
        if (($tx->status ?? 'COMPLETED') === 'VOIDED') {
            session()->flash('message', 'Transaksi sudah dibatalkan sebelumnya.');
            $this->closeVoidModal();
            return;
        }
        DB::transaction(function () use ($tx) {
            foreach ($tx->items as $item) {
                if ($item->product) {
                    app(\App\Services\HppCalculatorService::class)->restoreStockForVoid($item->product, (int) $item->quantity);
                }
            }
            $tx->update([
                'status' => 'VOIDED',
                'voided_at' => now(),
                'void_reason' => $this->voidReason ?: 'Dibatalkan oleh ' . (auth()->user()->name ?? 'Kasir'),
            ]);
        });
        $this->closeVoidModal();
        session()->flash('message', 'Transaksi ' . $tx->receipt_number . ' berhasil dibatalkan (VOID). Stok dikembalikan.');
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

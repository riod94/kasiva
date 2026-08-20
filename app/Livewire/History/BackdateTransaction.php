<?php

namespace App\Livewire\History;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Input Transaksi Lampau - Kasiva POS')]
class BackdateTransaction extends Component
{
    public string $totalAmount = '';
    public string $transactionDate = '';
    public string $transactionTime = '12:00';
    public string $paymentMethod = 'CASH';
    public string $notes = '';

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->isOwner() || auth()->user()->hasPermission('VIEW_TRANSACTIONS')), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk menginput omset lampau.');

        $this->transactionDate = now()->toDateString();
        $this->transactionTime = now()->format('H:i');
    }

    public function saveTransaction(): void
    {
        $this->validate([
            'totalAmount' => 'required|numeric|min:1',
            'transactionDate' => 'required|date',
            'transactionTime' => 'required',
            'paymentMethod' => 'required|in:CASH,QRIS,GOFOOD,GRABFOOD,SHOPEEFOOD',
        ]);

        $dateTime = \Carbon\Carbon::parse($this->transactionDate . ' ' . $this->transactionTime . ':00');
        $user = Auth::user();
        $cashierName = $user?->name ?? 'Kasir Utama';

        $receiptSuffix = strtoupper(substr(md5(uniqid()), 0, 5));
        $receiptNumber = 'BD-' . $receiptSuffix;
        $amount = (float)$this->totalAmount;

        $tx = new Transaction();
        $tx->timestamps = false;
        $tx->receipt_number = $receiptNumber;
        $tx->payment_method = $this->paymentMethod;
        $tx->total_amount = $amount;
        $tx->total_hpp = 0.0;
        $tx->paid_amount = $amount;
        $tx->change_amount = 0.0;
        $tx->cashier_name = $cashierName;
        $tx->sync_status = 'SYNCED';
        $tx->is_backdated = true;
        $tx->transaction_date = $dateTime;
        $tx->created_at = $dateTime;
        $tx->updated_at = $dateTime;
        $tx->save();

        TransactionItem::create([
            'transaction_id' => $tx->id,
            'product_id' => null,
            'product_name' => 'Entri Transaksi Manual (Lampau)',
            'unit_price' => $amount,
            'unit_hpp' => 0.0,
            'quantity' => 1,
            'subtotal' => $amount,
        ]);

        session()->flash('message', 'Transaksi masa lalu berhasil dicatat.');
        $this->redirect(route('history.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.history.backdate-transaction');
    }
}

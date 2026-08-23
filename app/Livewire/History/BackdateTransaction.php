<?php

namespace App\Livewire\History;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Input Transaksi Lampau - Kasiva POS')]
class BackdateTransaction extends Component
{
    public string $transactionDate = '';
    public string $transactionTime = '12:00';
    public string $paymentMethod = 'CASH';
    public string $search = '';
    public string $categoryFilter = 'all';

    /** @var array<int, array{product_id:string, name:string, price:float, hpp:float, qty:int, subtotal:float}> */
    public array $cart = [];

    public bool $affectStock = false;

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->isOwner() || auth()->user()->hasPermission('VIEW_TRANSACTIONS')), 403, 'Akses Ditolak');
        $this->transactionDate = now()->toDateString();
        $this->transactionTime = now()->format('H:i');
    }

    public function addToCart(string $productId): void
    {
        $product = Product::findOrFail($productId);
        foreach ($this->cart as $idx => $item) {
            if ($item['product_id'] === $productId) {
                $this->cart[$idx]['qty']++;
                $this->cart[$idx]['subtotal'] = $this->cart[$idx]['qty'] * $this->cart[$idx]['price'];
                return;
            }
        }
        $this->cart[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'hpp' => (float) $product->hpp,
            'qty' => 1,
            'subtotal' => (float) $product->price,
        ];
    }

    public function incrementQty(int $index): void
    {
        if (!isset($this->cart[$index])) return;
        $this->cart[$index]['qty']++;
        $this->cart[$index]['subtotal'] = $this->cart[$index]['qty'] * $this->cart[$index]['price'];
    }

    public function decrementQty(int $index): void
    {
        if (!isset($this->cart[$index])) return;
        $this->cart[$index]['qty']--;
        $this->cart[$index]['subtotal'] = $this->cart[$index]['qty'] * $this->cart[$index]['price'];
        if ($this->cart[$index]['qty'] <= 0) {
            array_splice($this->cart, $index, 1);
        }
    }

    public function removeFromCart(int $index): void
    {
        if (!isset($this->cart[$index])) return;
        array_splice($this->cart, $index, 1);
    }

    public function getTotalAmountProperty(): float
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    public function getTotalHppProperty(): float
    {
        $sum = 0;
        foreach ($this->cart as $item) $sum += $item['hpp'] * $item['qty'];
        return $sum;
    }

    public function saveTransaction(): void
    {
        $this->validate([
            'transactionDate' => 'required|date',
            'transactionTime' => 'required',
            'paymentMethod' => 'required|in:CASH,QRIS,GOFOOD,GRABFOOD,SHOPEEFOOD,SPLIT',
            'cart' => 'required|array|min:1',
        ], ['cart.required' => 'Keranjang tidak boleh kosong. Pilih minimal 1 produk.']);

        $dateTime = \Carbon\Carbon::parse($this->transactionDate . ' ' . $this->transactionTime . ':00');
        $user = Auth::user();
        $cashierName = $user?->name ?? 'Kasir Utama';
        $receiptNumber = 'BD-' . strtoupper(substr(md5(uniqid((string) rand(), true)), 0, 5)) . '-' . $dateTime->format('Ymd');

        DB::transaction(function () use ($dateTime, $cashierName, $receiptNumber) {
            $tx = new Transaction();
            $tx->timestamps = false;
            $tx->receipt_number = $receiptNumber;
            $tx->payment_method = $this->paymentMethod;
            $tx->total_amount = $this->totalAmount;
            $tx->total_hpp = $this->totalHpp;
            $tx->paid_amount = $this->totalAmount;
            $tx->change_amount = 0;
            $tx->cashier_name = $cashierName;
            $tx->sync_status = 'SYNCED';
            $tx->status = 'COMPLETED';
            $tx->is_backdated = true;
            $tx->transaction_date = $dateTime;
            $tx->created_at = $dateTime;
            $tx->updated_at = $dateTime;
            $tx->save();

            foreach ($this->cart as $item) {
                TransactionItem::create([
                    'transaction_id' => $tx->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'unit_hpp' => $item['hpp'],
                    'quantity' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);
                if ($this->affectStock) {
                    $product = Product::with('materials')->find($item['product_id']);
                    if ($product) {
                        app(\App\Services\HppCalculatorService::class)->deductRecipeStockForCheckout($product, (int) $item['qty']);
                    }
                }
            }
        });

        session()->flash('message', 'Transaksi lampau berhasil dicatat: ' . count($this->cart) . ' item, Rp ' . number_format($this->totalAmount, 0, ',', '.'));
        $this->redirect(route('history.index'), navigate: true);
    }

    public function render()
    {
        $productsQuery = Product::with('category')->where('is_active', true)
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter !== 'all', fn($q) => $q->where('category_id', $this->categoryFilter))
            ->orderBy('name');
        $products = $productsQuery->get();
        $categories = \App\Models\Category::orderBy('order_index')->get();
        return view('livewire.history.backdate-transaction', [
            'products' => $products,
            'categories' => $categories,
            'cartTotal' => $this->totalAmount,
            'cartHpp' => $this->totalHpp,
        ]);
    }
}

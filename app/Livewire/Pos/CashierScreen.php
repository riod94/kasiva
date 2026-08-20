<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\PaymentSetting;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\HppCalculatorService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CashierScreen extends Component
{
    public string $selectedCategory = 'ALL';
    public string $searchQuery = '';
    
    // Cart Structure: [item_key => ['id' => string, 'name' => string, 'sku' => string, 'price' => float, 'hpp' => float, 'qty' => int, 'variant_text' => string]]
    public array $cart = [];
    public float $subtotalAmount = 0.0;
    public float $totalAmount = 0.0;
    public float $totalHpp = 0.0;
    
    // Variant Selection Modal
    public ?Product $selectedProductForVariant = null;
    public bool $showVariantModal = false;
    public array $selectedOptions = [];

    // Checkout Flow States
    public bool $showCheckoutModal = false;
    public string $checkoutStep = 'METHOD_SELECT'; // 'METHOD_SELECT', 'CASH_FORM', 'QRIS_DISPLAY', 'PLATFORM_ADJUSTMENT', 'SPLIT_PAYMENT'
    public string $paymentMethod = 'CASH'; // CASH, QRIS, GOFOOD, GRABFOOD, SHOPEEFOOD, SPLIT
    
    // Cash Form State
    public float $paidAmount = 0.0;
    public float $changeAmount = 0.0;

    // Platform Adjustment State (GoFood, GrabFood, ShopeeFood)
    public string $selectedPlatform = 'GOFOOD';
    public float $adjustedAmount = 0.0; // Net received amount

    // Split Payment State
    public float $splitCashAmount = 0.0;
    public float $splitQrisAmount = 0.0;

    // Payment Settings
    public bool $enableGoFood = true;
    public bool $enableGrabFood = true;
    public bool $enableShopeeFood = true;
    public ?string $qrisImage = null;

    // Receipt Modal State
    public ?Transaction $lastTransaction = null;
    public bool $showReceiptModal = false;

    public function mount(): void
    {
        $this->enableGoFood = PaymentSetting::getValue('enable_gofood', 'true') === 'true';
        $this->enableGrabFood = PaymentSetting::getValue('enable_grabfood', 'true') === 'true';
        $this->enableShopeeFood = PaymentSetting::getValue('enable_shopeefood', 'true') === 'true';
        $this->qrisImage = PaymentSetting::getValue('qris_image', '/images/kasiva-logo-full.png');
        $this->calculateCart();
    }

    public function getGreetingProperty(): string
    {
        $hour = (int)date('H');
        if ($hour >= 5 && $hour < 11) return 'Selamat Pagi';
        if ($hour >= 11 && $hour < 15) return 'Selamat Siang';
        if ($hour >= 15 && $hour < 18) return 'Selamat Sore';
        return 'Selamat Malam';
    }

    public function selectCategory(string $categoryId): void
    {
        $this->selectedCategory = $categoryId;
    }

    public function handleProductClick(string $productId): void
    {
        $product = Product::with(['variants.options'])->find($productId);
        if ($product && $product->variants && $product->variants->isNotEmpty()) {
            $this->selectedProductForVariant = $product;
            $this->selectedOptions = [];
            // Select default options
            foreach ($product->variants as $variant) {
                if ($variant->options->isNotEmpty()) {
                    $this->selectedOptions[$variant->id] = $variant->options->first()->id;
                }
            }
            $this->showVariantModal = true;
        } else {
            $this->addToCart($productId);
        }
    }

    public function addToCart(string $productId, array $variantOptions = []): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        $extraPrice = 0.0;
        $variantNames = [];
        foreach ($variantOptions as $opt) {
            $extraPrice += (float)($opt['price_modifier'] ?? 0);
            $variantNames[] = $opt['name'];
        }

        $itemKey = $productId . (!empty($variantNames) ? '-' . implode('-', $variantNames) : '');
        $finalPrice = (float)$product->price + $extraPrice;
        $variantText = !empty($variantNames) ? implode(', ', $variantNames) : '';

        if (isset($this->cart[$itemKey])) {
            $this->cart[$itemKey]['qty']++;
        } else {
            $this->cart[$itemKey] = [
                'id' => $product->id,
                'name' => $product->name . ($variantText ? " ($variantText)" : ''),
                'sku' => $product->sku ?? '',
                'price' => $finalPrice,
                'hpp' => (float)$product->hpp,
                'qty' => 1,
                'variant_text' => $variantText,
            ];
        }
        $this->calculateCart();
    }

    public function confirmVariantSelection(): void
    {
        if (!$this->selectedProductForVariant) return;

        $optionsToPass = [];
        foreach ($this->selectedProductForVariant->variants as $variant) {
            if (isset($this->selectedOptions[$variant->id])) {
                $chosenOptId = $this->selectedOptions[$variant->id];
                $opt = $variant->options->firstWhere('id', $chosenOptId);
                if ($opt) {
                    $optionsToPass[] = [
                        'name' => $opt->name,
                        'price_modifier' => (float)$opt->price_modifier,
                    ];
                }
            }
        }

        $this->addToCart($this->selectedProductForVariant->id, $optionsToPass);
        $this->showVariantModal = false;
        $this->selectedProductForVariant = null;
        $this->selectedOptions = [];
    }

    public function updateQuantity(string $itemKey, int $newQty): void
    {
        if (isset($this->cart[$itemKey])) {
            if ($newQty <= 0) {
                unset($this->cart[$itemKey]);
            } else {
                $this->cart[$itemKey]['qty'] = $newQty;
            }
        }
        $this->calculateCart();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->calculateCart();
    }

    public function calculateCart(): void
    {
        $this->subtotalAmount = 0.0;
        $this->totalHpp = 0.0;

        foreach ($this->cart as $item) {
            $this->subtotalAmount += ($item['price'] * $item['qty']);
            $this->totalHpp += ($item['hpp'] * $item['qty']);
        }

        $this->totalAmount = $this->subtotalAmount;

        if ($this->paidAmount < $this->totalAmount || $this->paymentMethod !== 'CASH') {
            $this->paidAmount = $this->totalAmount;
        }

        $this->changeAmount = max(0.0, $this->paidAmount - $this->totalAmount);
    }

    public function openCheckoutModal(): void
    {
        if (empty($this->cart)) return;
        $this->calculateCart();
        $this->checkoutStep = 'METHOD_SELECT';
        $this->paidAmount = $this->totalAmount;
        $this->changeAmount = 0.0;
        $this->showCheckoutModal = true;
    }

    public function selectCashMethod(): void
    {
        $this->paymentMethod = 'CASH';
        $this->paidAmount = $this->totalAmount;
        $this->changeAmount = 0.0;
        $this->checkoutStep = 'CASH_FORM';
    }

    public function selectQrisMethod(): void
    {
        $this->paymentMethod = 'QRIS';
        $this->paidAmount = $this->totalAmount;
        $this->changeAmount = 0.0;
        $this->checkoutStep = 'QRIS_DISPLAY';
    }

    public function selectPlatformMethod(string $platform): void
    {
        $this->paymentMethod = $platform;
        $this->selectedPlatform = $platform;
        $this->adjustedAmount = $this->totalAmount;
        $this->checkoutStep = 'PLATFORM_ADJUSTMENT';
    }

    public function selectSplitMethod(): void
    {
        $this->paymentMethod = 'SPLIT';
        $this->splitCashAmount = round($this->totalAmount / 2, 0);
        $this->splitQrisAmount = $this->totalAmount - $this->splitCashAmount;
        $this->checkoutStep = 'SPLIT_PAYMENT';
    }

    public function setCashNominal(float $amount): void
    {
        $this->paidAmount = $amount;
        $this->changeAmount = max(0.0, $this->paidAmount - $this->totalAmount);
    }

    public function updatedPaidAmount(): void
    {
        $this->changeAmount = max(0.0, (float)$this->paidAmount - $this->totalAmount);
    }

    public function processCheckout(HppCalculatorService $calculator): void
    {
        if (empty($this->cart)) return;

        $receiptNumber = 'KSV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $user = Auth::user();
        $cashierName = $user?->name ?? 'Kasir Utama';

        $finalTotal = $this->totalAmount;
        $paid = (float)$this->paidAmount;
        $change = $this->changeAmount;
        $isAdjustment = false;
        $payments = null;

        if ($this->checkoutStep === 'PLATFORM_ADJUSTMENT') {
            $finalTotal = (float)$this->adjustedAmount;
            $paid = (float)$this->adjustedAmount;
            $change = 0.0;
            $isAdjustment = ($finalTotal !== $this->subtotalAmount);
        } elseif ($this->checkoutStep === 'QRIS_DISPLAY') {
            $paid = $this->totalAmount;
            $change = 0.0;
        } elseif ($this->checkoutStep === 'SPLIT_PAYMENT') {
            $paid = (float)$this->splitCashAmount + (float)$this->splitQrisAmount;
            $change = 0.0;
            $payments = [
                ['method' => 'TUNAI', 'amount' => (float)$this->splitCashAmount],
                ['method' => 'QRIS', 'amount' => (float)$this->splitQrisAmount],
            ];
        }

        $transaction = Transaction::create([
            'receipt_number' => $receiptNumber,
            'payment_method' => $this->paymentMethod,
            'total_amount' => $finalTotal,
            'total_hpp' => $this->totalHpp,
            'paid_amount' => $paid,
            'change_amount' => $change,
            'cashier_name' => $cashierName,
            'sync_status' => 'SYNCED',
        ]);

        foreach ($this->cart as $item) {
            $product = Product::find($item['id']);
            
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product ? $product->id : $item['id'],
                'product_name' => $item['name'],
                'unit_price' => $item['price'],
                'unit_hpp' => $item['hpp'],
                'quantity' => $item['qty'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);

            // Deduct recipe materials & product stock
            if ($product) {
                $calculator->deductRecipeStockForCheckout($product, $item['qty']);
            }
        }

        $this->lastTransaction = $transaction->load('items');
        $this->showCheckoutModal = false;
        $this->showReceiptModal = true;
        $this->cart = [];
        $this->calculateCart();
    }

    public function getWhatsAppReceiptUrlProperty(): string
    {
        if (!$this->lastTransaction) return '#';

        $text = "*STRUK DIGITAL KASIVA POS*\n";
        $text .= "No: " . $this->lastTransaction->receipt_number . "\n";
        $text .= "Tanggal: " . $this->lastTransaction->created_at->format('d/m/Y H:i') . "\n";
        $text .= "Kasir: " . $this->lastTransaction->cashier_name . "\n";
        $text .= "--------------------------------\n";
        foreach ($this->lastTransaction->items as $item) {
            $text .= $item->product_name . " (" . $item->quantity . "x) - Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
        }
        $text .= "--------------------------------\n";
        $text .= "TOTAL: Rp " . number_format($this->lastTransaction->total_amount, 0, ',', '.') . "\n";
        $text .= "BAYAR (" . $this->lastTransaction->payment_method . "): Rp " . number_format($this->lastTransaction->paid_amount, 0, ',', '.') . "\n";
        if ($this->lastTransaction->change_amount > 0) {
            $text .= "KEMBALI: Rp " . number_format($this->lastTransaction->change_amount, 0, ',', '.') . "\n";
        }
        $text .= "\nTerima kasih telah berbelanja di Kasiva!";

        return 'https://api.whatsapp.com/send?text=' . urlencode($text);
    }

    public function closeReceiptModal(): void
    {
        $this->showReceiptModal = false;
        $this->lastTransaction = null;
    }

    public function render()
    {
        $categories = Category::orderBy('order_index')->get();

        $query = Product::with(['recipes', 'variants.options'])->where('is_active', true);
        if ($this->selectedCategory !== 'ALL') {
            $query->where('category_id', $this->selectedCategory);
        }
        if (!empty($this->searchQuery)) {
            $query->where('name', 'like', '%' . $this->searchQuery . '%');
        }
        $products = $query->get();

        return view('livewire.pos.cashier-screen', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}

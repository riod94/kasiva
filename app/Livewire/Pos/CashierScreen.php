<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\CustomerReward;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\PaymentSetting;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\CampaignDiscountService;
use App\Services\HppCalculatorService;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CashierScreen extends Component
{
    public string $selectedCategory = 'ALL';
    public string $searchQuery = '';
    
    // Multi-cart: each cart = ['name'=>string, 'items'=> array]
    public array $carts = [];
    public int $activeCartIndex = 0;

    // Active cart proxy (kept for blade compat — always sync to carts[active])
    public array $cart = [];
    public float $subtotalAmount = 0.0;
    public float $discountTotal = 0.0;
    public array $discountDetails = [];
    public string $discountNote = '';
    public float $totalAmount = 0.0;
    public float $totalHpp = 0.0;
    
    // Variant Selection Modal
    public ?Product $selectedProductForVariant = null;
    public bool $showVariantModal = false;
    public array $selectedOptions = [];

    // Checkout Flow States
    public bool $showCheckoutModal = false;
    public string $checkoutStep = 'METHOD_SELECT';
    public string $paymentMethod = 'CASH';
    
    // Cash Form State
    public float $paidAmount = 0.0;
    public float $changeAmount = 0.0;

    // Platform Adjustment State
    public string $selectedPlatform = 'GOFOOD';
    public float $adjustedAmount = 0.0;

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

    // Loyalty Member (per-cart)
    public ?string $linkedMemberId = null;
    public ?string $appliedRewardId = null;
    public string $memberScanInput = '';
    public bool $showMemberScanner = false;
    public ?array $memberProgress = null;
    public array $availableRewards = [];

    public function mount(): void
    {
        $this->enableGoFood = PaymentSetting::getValue('enable_gofood', 'true') === 'true';
        $this->enableGrabFood = PaymentSetting::getValue('enable_grabfood', 'true') === 'true';
        $this->enableShopeeFood = PaymentSetting::getValue('enable_shopeefood', 'true') === 'true';
        $this->qrisImage = PaymentSetting::getValue('qris_image', '/images/kasiva-logo-full.png');

        $savedCarts = session()->get('kasiva.carts');
        $savedActive = session()->get('kasiva.active_cart', 0);
        if (is_array($savedCarts) && !empty($savedCarts)) {
            $this->carts = $savedCarts;
            $this->activeCartIndex = (int)$savedActive;
            if (!isset($this->carts[$this->activeCartIndex])) $this->activeCartIndex = 0;
            $this->cart = $this->carts[$this->activeCartIndex]['items'] ?? [];
            $this->linkedMemberId = $this->carts[$this->activeCartIndex]['member_id'] ?? null;
            $this->appliedRewardId = $this->carts[$this->activeCartIndex]['reward_id'] ?? null;
        } else {
            $this->carts = [['name' => 'Cart 1', 'items' => [], 'member_id' => null, 'reward_id' => null]];
            $this->activeCartIndex = 0;
            $this->cart = [];
            $this->linkedMemberId = null;
            $this->appliedRewardId = null;
        }
        $this->refreshMemberState();
        $this->calculateCart();
    }

    public function refreshMemberState(): void
    {
        $this->memberProgress = null;
        $this->availableRewards = [];
        if (!$this->linkedMemberId) return;
        $member = LoyaltyMember::with('stamps')->find($this->linkedMemberId);
        if (!$member) { $this->linkedMemberId = null; $this->appliedRewardId = null; return; }
        $program = LoyaltyProgram::where('is_active', true)->first();
        if ($program) {
            try { $this->memberProgress = LoyaltyService::getCustomerProgress($member, $program); } catch (\Throwable $e) {}
        }
        try { $this->availableRewards = LoyaltyService::getAvailableRewards($member)->toArray(); } catch (\Throwable $e) {}
        // if appliedReward no longer available, clear
        if ($this->appliedRewardId) {
            $still = collect($this->availableRewards)->contains(fn($r) => ($r['id'] ?? null) === $this->appliedRewardId);
            if (!$still) $this->appliedRewardId = null;
        }
    }

    private function persistCarts(): void
    {
        if (isset($this->carts[$this->activeCartIndex])) {
            $this->carts[$this->activeCartIndex]['items'] = $this->cart;
            $this->carts[$this->activeCartIndex]['member_id'] = $this->linkedMemberId;
            $this->carts[$this->activeCartIndex]['reward_id'] = $this->appliedRewardId;
        }
        session(['kasiva.carts' => $this->carts, 'kasiva.active_cart' => $this->activeCartIndex]);
    }

    public function switchCart(int $index): void
    {
        if (!isset($this->carts[$index])) return;
        $this->carts[$this->activeCartIndex]['items'] = $this->cart;
        $this->carts[$this->activeCartIndex]['member_id'] = $this->linkedMemberId;
        $this->carts[$this->activeCartIndex]['reward_id'] = $this->appliedRewardId;
        $this->activeCartIndex = $index;
        $this->cart = $this->carts[$this->activeCartIndex]['items'] ?? [];
        $this->linkedMemberId = $this->carts[$this->activeCartIndex]['member_id'] ?? null;
        $this->appliedRewardId = $this->carts[$this->activeCartIndex]['reward_id'] ?? null;
        $this->refreshMemberState();
        $this->calculateCart();
        $this->persistCarts();
    }

    public function createNewCart(): void
    {
        if (count($this->carts) >= 3) {
            session()->flash('message', 'Maksimal 3 keranjang aktif.');
            return;
        }
        $this->carts[$this->activeCartIndex]['items'] = $this->cart;
        $this->carts[$this->activeCartIndex]['member_id'] = $this->linkedMemberId;
        $this->carts[$this->activeCartIndex]['reward_id'] = $this->appliedRewardId;
        $this->carts[] = ['name' => 'Cart ' . (count($this->carts) + 1), 'items' => [], 'member_id' => null, 'reward_id' => null];
        $this->activeCartIndex = count($this->carts) - 1;
        $this->cart = [];
        $this->linkedMemberId = null; $this->appliedRewardId = null;
        $this->refreshMemberState();
        $this->calculateCart();
        $this->persistCarts();
    }

    public function holdCart(): void
    {
        $this->createNewCart();
    }

    public function closeCart(int $index): void
    {
        if (count($this->carts) <= 1) {
            $this->cart = [];
            $this->carts[0]['items'] = [];
            $this->calculateCart();
            $this->persistCarts();
            return;
        }
        array_splice($this->carts, $index, 1);
        // re-label
        foreach ($this->carts as $i => &$c) $c['name'] = 'Cart ' . ($i+1);
        if ($this->activeCartIndex >= count($this->carts)) $this->activeCartIndex = count($this->carts)-1;
        $this->cart = $this->carts[$this->activeCartIndex]['items'] ?? [];
        $this->calculateCart();
        $this->persistCarts();
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
        $this->persistCarts();
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
        $this->persistCarts();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->calculateCart();
        $this->persistCarts();
    }

    // ─── Loyalty Member links (per cart) ───
    public function linkMemberByQr(string $raw): void
    {
        $id = LoyaltyService::parseQrCode($raw);
        $member = null;
        if ($id) {
            // id dari parse adalah bagian setelah prefix, coba cari by qr_code suffix
            $member = LoyaltyMember::where('qr_code', 'like', '%' . $id)->first();
            if (!$member) $member = LoyaltyMember::where('id', $id)->first();
        }
        if (!$member) $member = LoyaltyMember::where('qr_code', $raw)->orWhere('phone', $raw)->first();
        if (!$member) { session()->flash('message','Member tidak ditemukan untuk QR/HP tersebut.'); return; }
        $this->linkedMemberId = $member->id;
        $this->appliedRewardId = null;
        $this->refreshMemberState();
        $this->persistCarts();
        session()->flash('message','Member terhubung: ' . ($member->name ?: $member->qr_code));
    }

    public function scanMemberResult(string $raw): void { $this->linkMemberByQr($raw); $this->showMemberScanner = false; }

    public function searchMember(): void
    {
        $q = trim($this->memberScanInput);
        if ($q === '') return;
        $this->linkMemberByQr($q);
        $this->memberScanInput = '';
    }

    public function unlinkMember(): void
    {
        $this->linkedMemberId = null; $this->appliedRewardId = null;
        $this->refreshMemberState(); $this->persistCarts();
    }

    public function applyReward(string $rewardId): void
    {
        $ok = collect($this->availableRewards)->contains(fn($r)=> ($r['id']??null)===$rewardId);
        if (!$ok) return;
        $this->appliedRewardId = $rewardId; $this->persistCarts();
    }
    public function removeReward(): void { $this->appliedRewardId = null; $this->persistCarts(); }

    public function getLinkedMemberProperty(): ?LoyaltyMember { return $this->linkedMemberId ? LoyaltyMember::find($this->linkedMemberId) : null; }

    public function calculateCart(): void
    {
        $this->subtotalAmount = 0.0;
        $this->totalHpp = 0.0;

        foreach ($this->cart as $item) {
            $this->subtotalAmount += ($item['price'] * $item['qty']);
            $this->totalHpp += ($item['hpp'] * $item['qty']);
        }

        // Campaign discount (marketing integration)
        try {
            $svc = app(CampaignDiscountService::class);
            $cartForDiscount = collect($this->cart)->map(fn($it) => [
                'product_id' => $it['id'],
                'price' => $it['price'],
                'qty' => $it['qty'],
                'name' => $it['name'],
            ])->values()->toArray();
            $res = $svc->calculate($cartForDiscount);
            $this->discountTotal = (float)($res['total'] ?? 0);
            $this->discountDetails = $res['details'] ?? [];
            $this->discountNote = $res['note'] ?? '';
        } catch (\Throwable $e) {
            $this->discountTotal = 0;
            $this->discountDetails = [];
            $this->discountNote = '';
        }

        $this->totalAmount = max(0, $this->subtotalAmount - $this->discountTotal);

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

        if ($this->checkoutStep === 'PLATFORM_ADJUSTMENT') {
            $finalTotal = (float)$this->adjustedAmount;
            $paid = (float)$this->adjustedAmount;
            $change = 0.0;
        } elseif ($this->checkoutStep === 'QRIS_DISPLAY') {
            $paid = $this->totalAmount;
            $change = 0.0;
        } elseif ($this->checkoutStep === 'SPLIT_PAYMENT') {
            $paid = (float)$this->splitCashAmount + (float)$this->splitQrisAmount;
            $change = 0.0;
        }

        $cartSnapshot = $this->cart;
        $snapshotHpp = $this->totalHpp;
        $snapshotDiscountNote = trim(($this->discountNote ? $this->discountNote : '') . ($this->appliedRewardId ? ($this->discountNote ? ', ' : '') . 'Loyalty Reward' : ''));
        $linkedId = $this->linkedMemberId;
        $rewardId = $this->appliedRewardId;
        $rewardProduct = null;
        $appliedProgram = LoyaltyProgram::where('is_active', true)->first();
        if ($rewardId && $appliedProgram && ($appliedProgram->reward_type ?? 'FREE_PRODUCT') === 'FREE_PRODUCT' && !empty($appliedProgram->reward_product_id)) {
            $rewardProduct = Product::find($appliedProgram->reward_product_id);
        }

        $transaction = null;
        DB::transaction(function () use (&$transaction, $receiptNumber, $cashierName, $finalTotal, $snapshotHpp, $paid, $change, $linkedId, $cartSnapshot, $rewardProduct, $rewardId) {
            $transaction = Transaction::create([
                'receipt_number' => $receiptNumber,
                'payment_method' => $this->paymentMethod,
                'total_amount' => $finalTotal,
                'total_hpp' => $snapshotHpp + ($rewardProduct ? (float)($rewardProduct->hpp ?: $rewardProduct->price * 0.45) : 0),
                'discount_total' => $this->discountTotal,
                'discount_note' => $this->discountNote ?: null,
                'loyalty_member_id' => $linkedId,
                'paid_amount' => $paid,
                'change_amount' => $change,
                'cashier_name' => $cashierName,
                'sync_status' => 'SYNCED',
            ]);

            foreach ($cartSnapshot as $item) {
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
                if ($product) app(HppCalculatorService::class)->deductRecipeStockForCheckout($product, $item['qty']);
            }
            if ($rewardProduct) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $rewardProduct->id,
                    'product_name' => '[GIFT] ' . $rewardProduct->name,
                    'unit_price' => 0,
                    'unit_hpp' => (float)($rewardProduct->hpp ?: $rewardProduct->price * 0.45),
                    'quantity' => 1,
                    'subtotal' => 0,
                ]);
            }
        });

        // Post-checkout loyalty (non-kritis, jangan gagalkan transaksi)
        if ($transaction && $linkedId) {
            try {
                $lp = LoyaltyProgram::where('is_active', true)->first();
                if ($lp) {
                    $cartProductIds = array_map(fn($it)=> (string)$it['id'], array_values($cartSnapshot));
                    $eligible = LoyaltyService::isStampEligible((float)$this->subtotalAmount, $this->discountTotal > 0, $cartProductIds, $lp);
                    if ($eligible) {
                        $m = LoyaltyMember::find($linkedId);
                        if ($m) {
                            LoyaltyService::addStamp($m, $lp, $transaction->id);
                            LoyaltyService::checkAndCreateReward($m, $lp);
                        }
                    }
                }
                if ($rewardId) {
                    LoyaltyService::claimReward($rewardId, $transaction->id);
                }
            } catch (\Throwable $e) {}
        }

        // loyalty flash before receipt
        if ($linkedId && isset($lp) && $lp) {
            try {
                $m = LoyaltyMember::with('stamps')->find($linkedId);
                if ($m) {
                    $prog = LoyaltyService::getCustomerProgress($m, $lp);
                    if ($prog['isEligibleForReward']) session()->flash('message', '🎉 Target Stamp Tercapai! Reward tersedia.');
                    else session()->flash('message', 'Stamp +1 (' . $prog['currentStamps'] . '/' . $prog['targetStamps'] . ') ✓');
                }
            } catch (\Throwable $e) {}
        }

        $this->lastTransaction = $transaction->load('items');
        $this->showCheckoutModal = false;
        $this->showReceiptModal = true;
        $this->cart = [];
        $this->carts[$this->activeCartIndex]['items'] = [];
        // clear member/reward for next order in same cart slot
        $this->linkedMemberId = null; $this->appliedRewardId = null;
        $this->carts[$this->activeCartIndex]['member_id'] = null;
        $this->carts[$this->activeCartIndex]['reward_id'] = null;
        $this->refreshMemberState();
        $this->calculateCart();
        $this->persistCarts();
    }

    public function getWhatsAppReceiptUrlProperty(): string
    {
        if (!$this->lastTransaction) return '#';

        $text = "*STRUK DIGITAL KASIVA POS*\n";
        $text .= "No: " . $this->lastTransaction->receipt_number . "\n";
        $text .= "Tanggal: " . $this->lastTransaction->created_at->format('d/m/Y H:i') . "\n";
        $text .= "Kasir: " . $this->lastTransaction->cashier_name . "\n";
        if ($this->lastTransaction->loyalty_member_id) {
            $lm = LoyaltyMember::find($this->lastTransaction->loyalty_member_id);
            if ($lm) $text .= "Member: " . ($lm->name ?: $lm->qr_code) . " (" . $lm->qr_code . ")\n";
        }
        if (($this->lastTransaction->discount_note ?? '') !== '') $text .= "Promo: " . $this->lastTransaction->discount_note . "\n";
        $text .= "--------------------------------\n";
        foreach ($this->lastTransaction->items as $item) {
            $text .= $item->product_name . " (" . $item->quantity . "x) - Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
        }
        $text .= "--------------------------------\n";
        if (($this->lastTransaction->discount_total ?? 0) > 0) {
            $text .= "DISKON: -Rp " . number_format($this->lastTransaction->discount_total, 0, ',', '.') . " (" . ($this->lastTransaction->discount_note ?? '') . ")\n";
        }
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

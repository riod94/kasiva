# Low-Level Design (LLD)

## Kasiva — Spesifikasi Detail Komponen & Class Architecture

| Metadata | Detail |
|---|---|
| **Namespace Utama** | `App\` |
| **Framework Base** | Laravel 13 |
| **Component System** | Livewire 4 Components & Blade Views |

---

## 1. Class Diagram & Komponen Core

```mermaid
classDiagram
    class Material {
        +UUID id
        +String name
        +String unit
        +Float current_stock
        +Float min_stock
        +Float avg_cost
        +Boolean is_active
        +products() BelongsToMany
    }

    class Product {
        +UUID id
        +UUID category_id
        +String name
        +String sku
        +Float price
        +Float hpp
        +Float current_stock
        +String image_url
        +Boolean is_active
        +category() BelongsTo
        +materials() BelongsToMany
        +variants() HasMany
        +getMarginTierAttribute() Array
    }

    class ProductRecipe {
        +UUID id
        +UUID product_id
        +UUID material_id
        +Float quantity
    }

    class Transaction {
        +UUID id
        +String receipt_number
        +Enum payment_method
        +Float total_amount
        +Float total_hpp
        +Float paid_amount
        +Float change_amount
        +Float platform_discount
        +Float platform_markup
        +Boolean is_backdated
        +String cashier_name
        +Enum sync_status
        +items() HasMany
    }

    class TransactionItem {
        +UUID id
        +UUID transaction_id
        +UUID product_id
        +String product_name
        +Float unit_price
        +Float unit_hpp
        +Integer quantity
        +Float subtotal
        +transaction() BelongsTo
        +product() BelongsTo
    }

    class HppCalculatorService {
        +recalculateMovingAverage(Material, float qty, float price) Float
        +calculateProductHpp(Product) Float
        +deductRecipeStockForCheckout(Product, int qty) void
    }

    class CashierScreen {
        +String selectedCategory
        +String searchQuery
        +Array cart
        +Float totalAmount
        +Float totalHpp
        +Boolean showCheckoutModal
        +Boolean showReceiptModal
        +addToCart(string productId) void
        +updateQuantity(string productId, int delta) void
        +processCheckout(HppCalculatorService) void
    }

    Product "1" -- "*" ProductRecipe
    Material "1" -- "*" ProductRecipe
    Product "*" -- "1" Category
    Transaction "1" -- "*" TransactionItem
    CashierScreen ..> HppCalculatorService
```

---

## 2. Detail Kontrak Interface Services

### 2.1 `HppCalculatorService` (`app/Services/HppCalculatorService.php`)

```php
namespace App\Services;

use App\Models\Material;
use App\Models\Product;

class HppCalculatorService
{
    /**
     * Recalculates moving average cost of a raw material upon restocking.
     * Formula: ((currentStock * currentAvgCost) + (incomingQty * incomingPrice)) / (currentStock + incomingQty)
     */
    public function recalculateMovingAverage(Material $material, float $incomingQty, float $incomingPrice): float;

    /**
     * Calculates the total COGS (HPP) for a product based on its ingredient recipes.
     */
    public function calculateProductHpp(Product $product): float;

    /**
     * Deducts material stock atomically during checkout.
     */
    public function deductRecipeStockForCheckout(Product $product, int $qty): void;
}
```

---

## 3. Responsive Breakpoints & Viewport Layout Rules

| Viewport Target | CSS Breakpoint | Komponen Keranjang (Cart) | Grid Produk |
|---|---|---|---|
| **Mobile (Smartphones)** | `< 768px` (`sm:`, default) | Floating Bottom Bar / Sheet Drawer Modal | 2 Kolom Card Grid |
| **Tablet (iPads / Android Tabs)** | `768px - 1024px` (`md:`) | Fixed Right Sidebar (Width 320px) | 3 Kolom Card Grid |
| **Desktop (PC Kasir)** | `> 1024px` (`lg:`, `xl:`) | Fixed Right Sidebar (Width 384px) | 4 Kolom Card Grid |

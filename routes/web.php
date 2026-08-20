<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Expenses\ExpenseManager;
use App\Livewire\History\BackdateTransaction;
use App\Livewire\History\TransactionHistory;
use App\Livewire\Inventory\CategoryManager;
use App\Livewire\Inventory\InventoryHub;
use App\Livewire\Inventory\MaterialManager;
use App\Livewire\Inventory\VariationManager;
use App\Livewire\LandingPage;
use App\Livewire\Marketing\BundleManager;
use App\Livewire\Marketing\CampaignManager;
use App\Livewire\Marketing\DiscountManager;
use App\Livewire\Marketing\LoyaltyManager;
use App\Livewire\Marketing\MarketingHub;
use App\Livewire\Marketing\MemberManager;
use App\Livewire\MobileOnboarding;
use App\Livewire\Pos\CashierScreen;
use App\Livewire\Profile\UserProfile;
use App\Livewire\Reports\FinancialReports;
use App\Livewire\Settings\OutletSettings;
use App\Livewire\Settings\PaymentSettings;
use App\Livewire\Settings\ProductManager;
use App\Livewire\Settings\ReceiptSettings;
use App\Livewire\Settings\RoleManager;
use App\Livewire\Settings\SettingsHub;
use App\Livewire\Settings\StaffManager;
use Illuminate\Support\Facades\Route;

// 1. SaaS Landing Page & Mobile Onboarding (Public)
Route::get('/', LandingPage::class)->name('landing');
Route::get('/onboarding', MobileOnboarding::class)->name('onboarding');

// 2. Halaman Informasi & Legal (Public)
Route::view('/about', 'livewire.pages.about')->name('about');
Route::view('/privacy', 'livewire.pages.privacy')->name('privacy');
Route::view('/terms', 'livewire.pages.terms')->name('terms');

Route::name('pages.')->group(function () {
    Route::view('/pages/about', 'livewire.pages.about')->name('about');
    Route::view('/pages/privacy', 'livewire.pages.privacy')->name('privacy');
    Route::view('/pages/terms', 'livewire.pages.terms')->name('terms');
});

// 3. Guest Routes (Hanya untuk pengguna belum login)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// 4. Authenticated SaaS & POS Routes (Wajib Login & Verifikasi RBAC)
Route::middleware(['auth'])->group(function () {
    // Profil & Akun (Dapat diakses seluruh pengguna terotentikasi)
    Route::get('/profile', UserProfile::class)->name('profile.show');

    // Kasir POS Main Screen
    Route::get('/pos', CashierScreen::class)
        ->middleware('permission:POS_ACCESS')
        ->name('pos.cashier');

    // Modul Inventaris & Bahan Baku
    Route::get('/inventory', InventoryHub::class)
        ->middleware('permission:VIEW_PRODUCTS,MANAGE_PRODUCTS,VIEW_MATERIALS,MANAGE_MATERIALS,MANAGE_CATEGORIES')
        ->name('inventory.index');
    Route::get('/inventory/products', ProductManager::class)
        ->middleware('permission:VIEW_PRODUCTS,MANAGE_PRODUCTS')
        ->name('inventory.products');
    Route::get('/inventory/categories', CategoryManager::class)
        ->middleware('permission:MANAGE_CATEGORIES')
        ->name('inventory.categories');
    Route::get('/inventory/materials', MaterialManager::class)
        ->middleware('permission:VIEW_MATERIALS,MANAGE_MATERIALS')
        ->name('inventory.materials');
    Route::get('/inventory/variations', VariationManager::class)
        ->middleware('permission:MANAGE_PRODUCTS')
        ->name('inventory.variations');

    // Modul Marketing & Loyalitas
    Route::get('/marketing', MarketingHub::class)
        ->middleware('permission:MANAGE_PROMOS,MANAGE_LOYALTY,MANAGE_MEMBERS')
        ->name('marketing.index');
    Route::get('/marketing/members', MemberManager::class)
        ->middleware('permission:VIEW_MEMBERS,MANAGE_MEMBERS')
        ->name('marketing.members');
    Route::get('/marketing/loyalty', LoyaltyManager::class)
        ->middleware('permission:MANAGE_LOYALTY')
        ->name('marketing.loyalty');
    Route::get('/marketing/bundles', BundleManager::class)
        ->middleware('permission:MANAGE_PROMOS')
        ->name('marketing.bundles');
    Route::get('/marketing/discounts', DiscountManager::class)
        ->middleware('permission:MANAGE_PROMOS')
        ->name('marketing.discounts');
    Route::get('/marketing/campaigns', CampaignManager::class)
        ->middleware('permission:MANAGE_PROMOS')
        ->name('marketing.campaigns');

    // Riwayat Transaksi & Pengeluaran Toko
    Route::get('/history', TransactionHistory::class)
        ->middleware('permission:VIEW_TRANSACTIONS')
        ->name('history.index');
    Route::get('/history/backdate', BackdateTransaction::class)
        ->middleware('permission:VIEW_TRANSACTIONS')
        ->name('history.backdate');
    Route::get('/expenses', ExpenseManager::class)
        ->middleware('permission:MANAGE_EXPENSES')
        ->name('expenses.index');

    // Laporan Keuangan & Margin
    Route::get('/reports', FinancialReports::class)
        ->middleware('permission:VIEW_REPORTS')
        ->name('reports.index');

    // Modul Pengaturan Detail
    Route::get('/settings', SettingsHub::class)
        ->middleware('permission:MANAGE_OUTLET,MANAGE_STAFF,MANAGE_ROLES,MANAGE_PAYMENTS,MANAGE_PRINTER')
        ->name('settings.index');
    Route::get('/settings/outlet', OutletSettings::class)
        ->middleware('permission:MANAGE_OUTLET')
        ->name('settings.outlet');
    Route::get('/settings/receipt', ReceiptSettings::class)
        ->middleware('permission:MANAGE_PRINTER')
        ->name('settings.receipt');
    Route::get('/settings/payment', PaymentSettings::class)
        ->middleware('permission:MANAGE_PAYMENTS')
        ->name('settings.payment');
    Route::get('/settings/staff', StaffManager::class)
        ->middleware('permission:MANAGE_STAFF')
        ->name('settings.staff');
    Route::get('/settings/roles', RoleManager::class)
        ->middleware('permission:MANAGE_ROLES')
        ->name('settings.roles');
    Route::get('/settings/products', ProductManager::class)
        ->middleware('permission:MANAGE_PRODUCTS')
        ->name('settings.products');
});

// 5. Error Pages Preview Routes (Local & Testing Only)
if (app()->environment('local', 'testing')) {
    Route::get('/error-preview/{code}', function ($code) {
        $validCodes = [400, 401, 403, 404, 419, 429, 500, 502, 503];
        $statusCode = (int)$code;
        if (!in_array($statusCode, $validCodes)) {
            abort(404, 'Kode error pengujian tidak ditemukan.');
        }
        $customMessage = request('message');
        if ($customMessage) {
            abort($statusCode, $customMessage);
        }
        abort($statusCode);
    })->name('error.preview');
}


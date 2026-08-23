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


Route::middleware('auth')->prefix('offline-sync')->group(function () {
    Route::post('/transactions', [\App\Http\Controllers\OfflineSyncController::class, 'transactions'])->middleware('permission:POS_ACCESS');
    Route::post('/expenses', [\App\Http\Controllers\OfflineSyncController::class, 'expenses'])->middleware('permission:MANAGE_EXPENSES');
});

Route::middleware('auth')->prefix('api/v1/sync')->group(function () {
    Route::post('/devices', [\App\Http\Controllers\SyncController::class, 'registerDevice']);
    Route::post('/push', [\App\Http\Controllers\SyncController::class, 'push']);
    Route::post('/pull', [\App\Http\Controllers\SyncController::class, 'pull']);
});

// Local-first operational app. Route ini tidak memakai Livewire agar dapat di-reload offline.
Route::view('/app/pos', 'pos.offline-shell')->name('app.pos');
Route::view('/app/history', 'pos.offline-shell')->name('app.history');
Route::view('/app/expenses', 'pos.offline-shell')->name('app.expenses');
Route::view('/app/members', 'pos.offline-shell')->name('app.members');
Route::view('/pos/offline', 'pos.offline-shell')->name('pos.offline');

// Phase 5: Admin/backoffice aliases — same Livewire pages under /admin/*
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/pos', \App\Livewire\Pos\CashierScreen::class)->middleware('permission:POS_ACCESS')->name('pos');
    Route::get('/history', \App\Livewire\History\TransactionHistory::class)->middleware('permission:VIEW_TRANSACTIONS')->name('history');
    Route::get('/history/backdate', \App\Livewire\History\BackdateTransaction::class)->middleware('permission:VIEW_TRANSACTIONS')->name('history.backdate');
    Route::get('/expenses', \App\Livewire\Expenses\ExpenseManager::class)->middleware('permission:MANAGE_EXPENSES')->name('expenses');
    Route::get('/inventory', \App\Livewire\Inventory\InventoryHub::class)->middleware('permission:VIEW_PRODUCTS,MANAGE_PRODUCTS,VIEW_MATERIALS,MANAGE_MATERIALS,MANAGE_CATEGORIES')->name('inventory');
    Route::get('/inventory/products', \App\Livewire\Settings\ProductManager::class)->middleware('permission:VIEW_PRODUCTS,MANAGE_PRODUCTS')->name('inventory.products');
    Route::get('/inventory/categories', \App\Livewire\Inventory\CategoryManager::class)->middleware('permission:MANAGE_CATEGORIES')->name('inventory.categories');
    Route::get('/inventory/materials', \App\Livewire\Inventory\MaterialManager::class)->middleware('permission:VIEW_MATERIALS,MANAGE_MATERIALS')->name('inventory.materials');
    Route::get('/inventory/variations', \App\Livewire\Inventory\VariationManager::class)->middleware('permission:MANAGE_PRODUCTS')->name('inventory.variations');
    Route::get('/marketing', \App\Livewire\Marketing\MarketingHub::class)->middleware('permission:MANAGE_PROMOS,MANAGE_LOYALTY,MANAGE_MEMBERS')->name('marketing');
    Route::get('/marketing/members', \App\Livewire\Marketing\MemberManager::class)->middleware('permission:VIEW_MEMBERS,MANAGE_MEMBERS')->name('marketing.members');
    Route::get('/marketing/loyalty', \App\Livewire\Marketing\LoyaltyManager::class)->middleware('permission:MANAGE_LOYALTY')->name('marketing.loyalty');
    Route::get('/marketing/bundles', \App\Livewire\Marketing\BundleManager::class)->middleware('permission:MANAGE_PROMOS')->name('marketing.bundles');
    Route::get('/marketing/discounts', \App\Livewire\Marketing\DiscountManager::class)->middleware('permission:MANAGE_PROMOS')->name('marketing.discounts');
    Route::get('/marketing/campaigns', \App\Livewire\Marketing\CampaignManager::class)->middleware('permission:MANAGE_PROMOS')->name('marketing.campaigns');
    Route::get('/reports', \App\Livewire\Reports\FinancialReports::class)->middleware('permission:VIEW_REPORTS')->name('reports');
    Route::get('/settings', \App\Livewire\Settings\SettingsHub::class)->middleware('permission:MANAGE_OUTLET,MANAGE_STAFF,MANAGE_ROLES,MANAGE_PAYMENTS,MANAGE_PRINTER')->name('settings');
    Route::get('/settings/outlet', \App\Livewire\Settings\OutletSettings::class)->middleware('permission:MANAGE_OUTLET')->name('settings.outlet');
    Route::get('/settings/receipt', \App\Livewire\Settings\ReceiptSettings::class)->middleware('permission:MANAGE_PRINTER')->name('settings.receipt');
    Route::get('/settings/payment', \App\Livewire\Settings\PaymentSettings::class)->middleware('permission:MANAGE_PAYMENTS')->name('settings.payment');
    Route::get('/settings/staff', \App\Livewire\Settings\StaffManager::class)->middleware('permission:MANAGE_STAFF')->name('settings.staff');
    Route::get('/settings/roles', \App\Livewire\Settings\RoleManager::class)->middleware('permission:MANAGE_ROLES')->name('settings.roles');
    Route::get('/settings/products', \App\Livewire\Settings\ProductManager::class)->middleware('permission:MANAGE_PRODUCTS')->name('settings.products');
});

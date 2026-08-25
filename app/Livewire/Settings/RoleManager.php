<?php

namespace App\Livewire\Settings;

use App\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Hak Akses & Peran - Kasiva POS')]
class RoleManager extends Component
{
    public bool $showModal = false;

    public ?string $roleId = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public array $selectedPermissions = ['POS_ACCESS'];

    public array $permissionCategories = [
        'transaction' => ['label' => 'Transaksi & Kasir', 'icon' => 'shopping-cart'],
        'inventory' => ['label' => 'Produk & Stok', 'icon' => 'box'],
        'marketing' => ['label' => 'Promosi & Pelanggan', 'icon' => 'sparkles'],
        'finance' => ['label' => 'Keuangan & Laporan', 'icon' => 'chart-bar'],
        'system' => ['label' => 'Pengaturan Sistem', 'icon' => 'settings'],
    ];

    public array $allPermissions = [
        ['id' => 'POS_ACCESS', 'label' => 'Akses Menu Kasir POS', 'icon' => 'shopping-cart', 'category' => 'transaction'],
        ['id' => 'VIEW_TRANSACTIONS', 'label' => 'Lihat Riwayat Penjualan', 'icon' => 'document-text', 'category' => 'transaction'],
        ['id' => 'VOID_TRANSACTION', 'label' => 'Batalkan / Hapus Transaksi', 'icon' => 'trash', 'category' => 'transaction'],

        ['id' => 'VIEW_PRODUCTS', 'label' => 'Lihat Katalog Produk', 'icon' => 'grid', 'category' => 'inventory'],
        ['id' => 'MANAGE_PRODUCTS', 'label' => 'Kelola Produk & Varian', 'icon' => 'plus', 'category' => 'inventory'],
        ['id' => 'VIEW_MATERIALS', 'label' => 'Lihat Stok Bahan Baku', 'icon' => 'box', 'category' => 'inventory'],
        ['id' => 'MANAGE_MATERIALS', 'label' => 'Kelola Bahan Baku & Restok', 'icon' => 'arrow-up', 'category' => 'inventory'],
        ['id' => 'MANAGE_CATEGORIES', 'label' => 'Kelola Kategori Produk', 'icon' => 'tag', 'category' => 'inventory'],

        ['id' => 'MANAGE_PROMOS', 'label' => 'Kelola Promo & Bundle', 'icon' => 'sparkles', 'category' => 'marketing'],
        ['id' => 'VIEW_MEMBERS', 'label' => 'Lihat Database Pelanggan', 'icon' => 'users', 'category' => 'marketing'],
        ['id' => 'MANAGE_MEMBERS', 'label' => 'Kelola Data Pelanggan', 'icon' => 'users', 'category' => 'marketing'],
        ['id' => 'MANAGE_LOYALTY', 'label' => 'Kelola Program Loyalty/Stempel', 'icon' => 'sparkles', 'category' => 'marketing'],

        ['id' => 'VIEW_REPORTS', 'label' => 'Lihat Laporan Keuangan & HPP', 'icon' => 'chart-bar', 'category' => 'finance'],
        ['id' => 'MANAGE_EXPENSES', 'label' => 'Kelola Biaya & Pengeluaran', 'icon' => 'arrow-up', 'category' => 'finance'],

        ['id' => 'MANAGE_OUTLET', 'label' => 'Kelola Informasi Outlet', 'icon' => 'store', 'category' => 'system'],
        ['id' => 'MANAGE_PRINTER', 'label' => 'Pengaturan Struk & Printer', 'icon' => 'printer', 'category' => 'system'],
        ['id' => 'MANAGE_PAYMENTS', 'label' => 'Pengaturan Metode Pembayaran', 'icon' => 'credit-card', 'category' => 'system'],
        ['id' => 'MANAGE_STAFF', 'label' => 'Kelola Staff & Hak Akses', 'icon' => 'users', 'category' => 'system'],
        ['id' => 'MANAGE_ROLES', 'label' => 'Kelola Peran & Izin Role', 'icon' => 'settings', 'category' => 'system'],
    ];

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->isOwner() || auth()->user()->hasPermission('MANAGE_ROLES')), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola peran dan hak akses.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['roleId', 'name', 'slug', 'description']);
        $this->selectedPermissions = ['POS_ACCESS'];
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->slug = $role->slug;
        $this->description = $role->description ?? '';
        $this->selectedPermissions = $role->permissions->pluck('slug')->toArray();
        if (empty($this->selectedPermissions)) {
            $this->selectedPermissions = ['POS_ACCESS', 'VIEW_TRANSACTIONS'];
        }
        $this->showModal = true;
    }

    public function togglePermission(string $permId): void
    {
        if (in_array($permId, $this->selectedPermissions)) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, [$permId]));
        } else {
            $this->selectedPermissions[] = $permId;
        }
    }

    public function saveRole(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
        ]);

        $slug = $this->slug ?: str()->slug($this->name);

        $role = Role::updateOrCreate(
            ['id' => $this->roleId],
            [
                'name' => $this->name,
                'slug' => $slug,
                'description' => $this->description,
            ]
        );

        // Sync permissions
        $role->syncPermissions($this->selectedPermissions);

        $this->showModal = false;
        session()->flash('message', 'Peran dan hak akses berhasil disimpan.');
    }

    public function deleteRole(string $id): void
    {
        $role = Role::findOrFail($id);
        if ($role->slug === 'owner') {
            session()->flash('error', 'Peran Owner/Super Admin tidak dapat dihapus.');

            return;
        }
        $role->delete();
        session()->flash('message', 'Peran berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.settings.role-manager', [
            'roles' => Role::with('permissions')->get(),
        ]);
    }
}

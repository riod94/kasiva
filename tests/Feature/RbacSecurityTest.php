<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RbacSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $cashier;

    protected Role $roleOwner;

    protected Role $roleCashier;

    protected function setUp(): void
    {
        parent::setUp();

        $outlet = Outlet::create([
            'name' => 'Kasiva Test Outlet',
            'tax_percentage' => 10,
            'service_charge_percentage' => 5,
        ]);

        $this->roleOwner = Role::create([
            'name' => 'Owner',
            'slug' => 'owner',
        ]);

        $this->roleCashier = Role::create([
            'name' => 'Staf Kasir',
            'slug' => 'cashier',
        ]);

        $permissions = [
            'POS_ACCESS', 'VIEW_TRANSACTIONS', 'VOID_TRANSACTION', 'VIEW_PRODUCTS',
            'MANAGE_PRODUCTS', 'VIEW_MATERIALS', 'MANAGE_MATERIALS', 'MANAGE_CATEGORIES',
            'MANAGE_PROMOS', 'VIEW_MEMBERS', 'MANAGE_MEMBERS', 'MANAGE_LOYALTY',
            'VIEW_REPORTS', 'MANAGE_EXPENSES', 'MANAGE_OUTLET', 'MANAGE_PRINTER',
            'MANAGE_PAYMENTS', 'MANAGE_STAFF', 'MANAGE_ROLES',
        ];

        foreach ($permissions as $slug) {
            Permission::create(['slug' => $slug, 'name' => $slug]);
        }

        $this->roleOwner->syncPermissions($permissions);
        $this->roleCashier->syncPermissions(['POS_ACCESS', 'VIEW_TRANSACTIONS']);

        $this->owner = User::create([
            'name' => 'Owner Test',
            'email' => 'owner@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->roleOwner->id,
            'outlet_id' => $outlet->id,
        ]);

        $this->cashier = User::create([
            'name' => 'Cashier Test',
            'email' => 'cashier@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->roleCashier->id,
            'outlet_id' => $outlet->id,
        ]);
    }

    public function test_owner_has_full_bypass_access(): void
    {
        $this->assertTrue($this->owner->isOwner());
        $this->assertTrue($this->owner->hasPermission('VIEW_REPORTS'));
        $this->assertTrue($this->owner->hasPermission('MANAGE_ROLES'));
        $this->assertTrue($this->owner->hasPermission('MANAGE_PROMOS'));
        $this->assertTrue($this->owner->hasPermission('NON_EXISTENT_PERM'));
    }

    public function test_cashier_has_restricted_permissions(): void
    {
        $this->assertFalse($this->cashier->isOwner());
        $this->assertTrue($this->cashier->hasPermission('POS_ACCESS'));
        $this->assertTrue($this->cashier->hasPermission('VIEW_TRANSACTIONS'));
        $this->assertFalse($this->cashier->hasPermission('MANAGE_PROMOS'));
        $this->assertFalse($this->cashier->hasPermission('VIEW_MEMBERS'));
        $this->assertFalse($this->cashier->hasPermission('MANAGE_MEMBERS'));
        $this->assertFalse($this->cashier->hasPermission('MANAGE_LOYALTY'));
        $this->assertFalse($this->cashier->hasPermission('VIEW_REPORTS'));
        $this->assertFalse($this->cashier->hasPermission('MANAGE_ROLES'));
        $this->assertFalse($this->cashier->hasPermission('MANAGE_EXPENSES'));
    }

    public function test_owner_can_access_all_modules(): void
    {
        // Marketing
        $this->actingAs($this->owner)->get(route('marketing.index'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('marketing.members'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('marketing.loyalty'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('marketing.discounts'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('marketing.bundles'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('marketing.campaigns'))->assertStatus(200);

        // Reports & Expenses
        $this->actingAs($this->owner)->get(route('reports.index'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('expenses.index'))->assertStatus(200);

        // Settings
        $this->actingAs($this->owner)->get(route('settings.index'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('settings.roles'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('settings.staff'))->assertStatus(200);
    }

    public function test_cashier_is_strictly_blocked_from_marketing_and_other_modules(): void
    {
        // Marketing Blocked (403)
        $this->actingAs($this->cashier)->get(route('marketing.index'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('marketing.members'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('marketing.loyalty'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('marketing.discounts'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('marketing.bundles'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('marketing.campaigns'))->assertStatus(403);

        // Inventory Blocked (403)
        $this->actingAs($this->cashier)->get(route('inventory.index'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('inventory.products'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('inventory.materials'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('inventory.categories'))->assertStatus(403);

        // Reports & Expenses Blocked (403)
        $this->actingAs($this->cashier)->get(route('reports.index'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('expenses.index'))->assertStatus(403);

        // Settings Blocked (403)
        $this->actingAs($this->cashier)->get(route('settings.index'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('settings.roles'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('settings.staff'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('settings.outlet'))->assertStatus(403);
    }

    public function test_cashier_can_only_access_pos_history_and_profile(): void
    {
        $this->actingAs($this->cashier)->get(route('pos.cashier'))->assertStatus(200);
        $this->actingAs($this->cashier)->get(route('history.index'))->assertStatus(200);
        $this->actingAs($this->cashier)->get(route('profile.show'))->assertStatus(200);
    }
}

<?php

use App\Contracts\SyncRepositoryInterface;
use App\DTO\CatalogItem;
use App\DTO\SyncOperation;
use App\DTO\TransactionSyncData;
use App\Models\Expense;
use App\Models\Product;
use App\Models\SyncDevice;
use App\Models\SyncQueue;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\KasivaTestFixturesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KasivaTestFixturesSeeder::class);
});

describe('SyncOperation DTO', function () {
    it('creates with default client_operation_id and status', function () {
        $op = new SyncOperation(
            operation: 'UPSERT_TRANSACTION',
            entityType: 'transaction',
            entityId: null,
            payload: ['foo' => 'bar'],
        );

        expect($op->clientOperationId)->not->toBeEmpty()
            ->and($op->status)->toBe('PENDING')
            ->and($op->attempts)->toBe(0);
    });

    it('serializes and deserializes correctly via toArray/fromArray', function () {
        $op = new SyncOperation(
            operation: 'UPSERT_EXPENSE',
            entityType: 'expense',
            entityId: 'exp-1',
            payload: ['title' => 'Test', 'amount' => 5000],
            clientOperationId: 'op-123',
            status: SyncOperation::STATUS_SENDING,
            attempts: 2,
        );

        $array = $op->toArray();

        expect($array['client_operation_id'])->toBe('op-123')
            ->and($array['operation'])->toBe('UPSERT_EXPENSE')
            ->and($array['payload'])->toBe(['title' => 'Test', 'amount' => 5000])
            ->and($array['status'])->toBe('SENDING')
            ->and($array['attempts'])->toBe(2);

        $restored = SyncOperation::fromArray($array);
        expect($restored->operation)->toBe('UPSERT_EXPENSE')
            ->and($restored->entityType)->toBe('expense')
            ->and($restored->entityId)->toBe('exp-1')
            ->and($restored->payload)->toBe(['title' => 'Test', 'amount' => 5000])
            ->and($restored->status)->toBe('SENDING')
            ->and($restored->attempts)->toBe(2);
    });

    it('identifies terminal states correctly', function () {
        expect((new SyncOperation('UPSERT_MEMBER', 'member', null, []))->isTerminalState())->toBeFalse()
            ->and((new SyncOperation('UPSERT_MEMBER', 'member', null, [], null, SyncOperation::STATUS_SYNCED))->isTerminalState())->toBeTrue()
            ->and((new SyncOperation('UPSERT_MEMBER', 'member', null, [], null, SyncOperation::STATUS_CONFLICT))->isTerminalState())->toBeTrue()
            ->and((new SyncOperation('UPSERT_MEMBER', 'member', null, [], null, SyncOperation::STATUS_FAILED))->isTerminalState())->toBeFalse();
    });

    it('increments attempts', function () {
        $op = new SyncOperation('UPSERT_MEMBER', 'member', null, []);
        $op->incrementAttempts();
        $op->incrementAttempts();

        expect($op->attempts)->toBe(2);
    });
});

describe('CatalogItem DTO', function () {
    it('constructs from array with aliases', function () {
        $item = CatalogItem::fromArray([
            'id' => 'prod-1',
            'name' => 'Espresso',
            'sku' => 'KSV-TEST-001',
            'price' => 10000,
            'hpp' => 1500,
            'stock' => 100,
            'is_active' => true,
            'category_id' => 'cat-1',
        ]);

        expect($item->id)->toBe('prod-1')
            ->and($item->currentStock)->toBe(100.0)
            ->and($item->isActive)->toBeTrue();
    });

    it('converts to array preserving fields', function () {
        $item = new CatalogItem(
            id: 'prod-2',
            name: 'Milk Tea',
            sku: 'KSV-TEST-002',
            price: 12000.0,
            hpp: 3000.0,
            currentStock: 100.0,
            isActive: true,
            categoryId: 'cat-1',
        );

        $array = $item->toArray();

        expect($array['id'])->toBe('prod-2')
            ->and($array['name'])->toBe('Milk Tea')
            ->and($array['current_stock'])->toBe(100.0)
            ->and($array['is_active'])->toBe(true);
    });

    it('builds from Product model with relations', function () {
        $product = Product::where('sku', 'KSV-TEST-001')->first();

        $item = CatalogItem::fromModel($product->load(['category', 'variants.options', 'recipes']));

        expect($item->id)->toBe($product->id)
            ->and($item->name)->toBe('Espresso')
            ->and($item->price)->toBe(10000.0)
            ->and($item->categoryId)->toBe($product->category_id);
    });
});

describe('TransactionSyncData DTO', function () {
    it('round-trips through toArray and fromArray', function () {
        $data = new TransactionSyncData(
            clientTransactionId: 'client-tx-1',
            receiptNumber: 'KSV-20260823-0001',
            paymentMethod: 'CASH',
            totalAmount: 10000.0,
            totalHpp: 1500.0,
            paidAmount: 10000.0,
            changeAmount: 0.0,
            discountTotal: 0.0,
            discountNote: null,
            cashierName: 'Test Cashier',
            loyaltyMemberId: null,
        );

        $array = $data->toArray();
        $restored = TransactionSyncData::fromArray($array);

        expect($restored->clientTransactionId)->toBe('client-tx-1')
            ->and($restored->receiptNumber)->toBe('KSV-20260823-0001')
            ->and($restored->paymentMethod)->toBe('CASH')
            ->and($restored->totalAmount)->toBe(10000.0)
            ->and($restored->totalHpp)->toBe(1500.0)
            ->and($restored->paidAmount)->toBe(10000.0)
            ->and($restored->changeAmount)->toBe(0.0)
            ->and($restored->cashierName)->toBe('Test Cashier');
    });

    it('preserves line items in payload', function () {
        $data = new TransactionSyncData(
            clientTransactionId: 'client-tx-2',
            receiptNumber: 'KSV-20260823-0002',
            paymentMethod: 'QRIS',
            totalAmount: 12000.0,
            totalHpp: 3000.0,
            paidAmount: 12000.0,
            changeAmount: 0.0,
            items: [
                ['product_id' => 'prod-2', 'product_name' => 'Milk Tea', 'unit_price' => 12000, 'quantity' => 1, 'subtotal' => 12000],
            ],
        );

        $restored = TransactionSyncData::fromArray($data->toArray());

        expect($restored->items)->toBe([
            ['product_id' => 'prod-2', 'product_name' => 'Milk Tea', 'unit_price' => 12000, 'quantity' => 1, 'subtotal' => 12000],
        ]);
    });
});

describe('Model schema parity with contracts', function () {
    it('Transaction has all contract-required fields', function () {
        $tx = new Transaction;

        expect($tx->getFillable())->toContain('receipt_number')
            ->and($tx->getFillable())->toContain('payment_method')
            ->and($tx->getFillable())->toContain('total_amount')
            ->and($tx->getFillable())->toContain('total_hpp')
            ->and($tx->getFillable())->toContain('paid_amount')
            ->and($tx->getFillable())->toContain('change_amount')
            ->and($tx->getFillable())->toContain('loyalty_member_id')
            ->and($tx->getFillable())->toContain('cashier_name')
            ->and($tx->getFillable())->toContain('sync_status')
            ->and($tx->getFillable())->toContain('client_transaction_id');
    });

    it('Expense has client_expense_id and sync_status', function () {
        $exp = new Expense;

        expect($exp->getFillable())->toContain('title')
            ->and($exp->getFillable())->toContain('amount')
            ->and($exp->getFillable())->toContain('category')
            ->and($exp->getFillable())->toContain('expense_date')
            ->and($exp->getFillable())->toContain('client_expense_id')
            ->and($exp->getFillable())->toContain('sync_status');
    });

    it('sync_queue has status and client_operation_id columns', function () {
        expect(Schema::hasColumn('sync_queue', 'status'))->toBeTrue()
            ->and(Schema::hasColumn('sync_queue', 'client_operation_id'))->toBeTrue();
    });

    it('Product has catalog fields required by CatalogRepository', function () {
        $product = new Product;

        expect($product->getFillable())->toContain('name')
            ->and($product->getFillable())->toContain('sku')
            ->and($product->getFillable())->toContain('price')
            ->and($product->getFillable())->toContain('hpp')
            ->and($product->getFillable())->toContain('current_stock')
            ->and($product->getFillable())->toContain('is_active')
            ->and($product->getFillable())->toContain('category_id');
    });
});

describe('Sync invariants', function () {
    it('enqueueing a transaction requires outbox entry (sync invariant)', function () {
        $device = SyncDevice::create([
            'id' => (string) Str::uuid(),
            'user_id' => User::first()->id,
            'platform' => 'web',
        ]);

        $opId = app(SyncRepositoryInterface::class)->enqueueOperation(
            'UPSERT_TRANSACTION',
            'transaction',
            null,
            ['client_transaction_id' => (string) Str::uuid()],
            $device->id
        );

        expect($opId)->not->toBeEmpty()
            ->and(SyncQueue::find($opId)->operation)->toBe('UPSERT_TRANSACTION');
    });
});

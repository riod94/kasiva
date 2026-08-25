<?php

namespace App\Services;

use App\Models\CustomerReward;
use App\Models\Expense;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyStamp;
use App\Models\Product;
use App\Models\SyncQueue;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SyncQueueProcessor
{
    public function process(SyncQueue $entry): string
    {
        if ($entry->processed_at) {
            return 'SYNCED';
        }
        try {
            DB::transaction(function () use ($entry): void {
                match ($entry->operation) {
                    'UPSERT_EXPENSE' => $this->expense($entry->payload),
                    'UPSERT_TRANSACTION' => $this->transaction($entry->payload),
                    'UPSERT_MEMBER' => $this->member($entry->payload),
                    'ADD_LOYALTY_STAMP' => $this->stamp($entry->payload),
                    'UPSERT_CUSTOMER_REWARD' => $this->reward($entry->payload),
                    default => throw ValidationException::withMessages(['operation' => 'Operasi sync tidak didukung.']),
                };
                $entry->forceFill(['processed_at' => now(), 'last_error' => null])->save();
            });

            return 'SYNCED';
        } catch (\InvalidArgumentException $e) {
            $entry->increment('attempts');
            $status = str_starts_with($e->getMessage(), 'STOCK_CONFLICT:') ? 'CONFLICT' : 'RETRY';
            $entry->update(['last_error' => $e->getMessage(), 'available_at' => $status === 'RETRY' ? now()->addSeconds(min(32, 2 ** min(5, $entry->attempts))) : null]);

            return $status;
        } catch (ValidationException $e) {
            $entry->increment('attempts');
            $entry->update(['last_error' => $e->getMessage(), 'available_at' => now()->addSeconds(min(32, 2 ** min(5, $entry->attempts)))]);

            return 'RETRY';
        }
    }

    private function member(array $payload): void
    {
        validator($payload, ['client_member_id' => 'required|uuid', 'name' => 'nullable|string|max:255', 'phone' => 'nullable|string|max:40', 'qr_code' => 'required|string|max:80', 'status' => 'required|string|max:30', 'stamps_count' => 'integer|min:0', 'total_visits' => 'integer|min:0'])->validate();
        LoyaltyMember::firstOrCreate(['id' => $payload['client_member_id']], collect($payload)->except('client_member_id')->toArray());
    }

    private function stamp(array $payload): void
    {
        validator($payload, ['client_stamp_id' => 'required|uuid', 'loyalty_member_id' => 'required|uuid', 'program_id' => 'required|uuid', 'stamps_earned' => 'required|integer|min:1', 'stamped_at' => 'required'])->validate();
        if (LoyaltyStamp::whereKey($payload['client_stamp_id'])->exists()) {
            return;
        }
        $member = LoyaltyMember::lockForUpdate()->findOrFail($payload['loyalty_member_id']);
        LoyaltyStamp::create(['id' => $payload['client_stamp_id'], 'loyalty_member_id' => $member->id, 'program_id' => $payload['program_id'], 'transaction_id' => $payload['transaction_id'] ?? null, 'stamps_earned' => $payload['stamps_earned'], 'stamped_at' => $payload['stamped_at']]);
        $member->increment('stamps_count', (int) $payload['stamps_earned']);
        $member->increment('total_visits');
    }

    private function reward(array $payload): void
    {
        validator($payload, ['client_reward_id' => 'required|uuid', 'loyalty_member_id' => 'required|uuid', 'program_id' => 'required|uuid', 'status' => 'required|string|max:30', 'available_at' => 'required'])->validate();
        CustomerReward::firstOrCreate(['id' => $payload['client_reward_id']], collect($payload)->except('client_reward_id')->toArray());
    }

    private function expense(array $payload): void
    {
        $payload['client_expense_id'] = $payload['client_expense_id'] ?? $payload['entity_id'] ?? (string) Str::uuid();
        $payload['category'] = $payload['category'] ?? 'OPERATIONAL';
        $payload['expense_date'] = $payload['expense_date'] ?? now()->toDateString();
        $payload['notes'] = $payload['notes'] ?? null;
        validator($payload, ['client_expense_id' => 'required|uuid', 'title' => 'required|string|max:255', 'amount' => 'required|numeric|min:1', 'category' => 'required|string|max:100', 'expense_date' => 'required', 'notes' => 'nullable|string'])->validate();
        Expense::firstOrCreate(['client_expense_id' => $payload['client_expense_id']], $payload + ['sync_status' => 'SYNCED']);
    }

    private function transaction(array $payload): void
    {
        validator($payload, [
            'client_transaction_id' => 'required|uuid',
            'receipt_number' => 'required|string|max:80',
            'payment_method' => 'required|in:CASH,QRIS_STATIC',
            'total_amount' => 'required|numeric|min:0',
            'total_hpp' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'change_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid',
            'items.*.product_name' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.unit_hpp' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|numeric|min:0',
        ])->validate();

        $transaction = Transaction::firstOrCreate(
            ['client_transaction_id' => $payload['client_transaction_id']],
            collect($payload)->except('items')->merge(['sync_status' => 'SYNCED'])->toArray()
        );
        if (! $transaction->wasRecentlyCreated) {
            return;
        }

        foreach ($payload['items'] as $item) {
            $product = Product::query()->lockForUpdate()->find($item['product_id']);
            if (! $product || (float) $product->current_stock < (int) $item['quantity']) {
                throw new \InvalidArgumentException('STOCK_CONFLICT: stok produk tidak mencukupi.');
            }
            $product->decrement('current_stock', (int) $item['quantity']);
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'product_name' => $item['product_name'],
                'unit_price' => $item['unit_price'],
                'unit_hpp' => $item['unit_hpp'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
            ]);
        }
    }
}

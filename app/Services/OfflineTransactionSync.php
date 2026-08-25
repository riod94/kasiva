<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OfflineTransactionSync
{
    public function sync(array $payload, ?string $cashierName = null): Transaction
    {
        $data = validator($payload, [
            'client_transaction_id' => ['required', 'uuid'], 'receipt_number' => ['required', 'string', 'max:80'],
            'payment_method' => ['required', 'in:CASH,QRIS_STATIC'], 'total_amount' => ['required', 'numeric', 'min:0'],
            'total_hpp' => ['required', 'numeric', 'min:0'], 'paid_amount' => ['required', 'numeric', 'min:0'],
            'change_amount' => ['required', 'numeric', 'min:0'], 'payment_confirmed_manually' => ['boolean'],
            'items' => ['required', 'array', 'min:1'], 'items.*.product_id' => ['required', 'uuid'],
            'items.*.product_name' => ['required', 'string'], 'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.unit_hpp' => ['required', 'numeric', 'min:0'], 'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.subtotal' => ['required', 'numeric', 'min:0'],
        ])->validate();
        if ($data['payment_method'] === 'QRIS_STATIC' && empty($data['payment_confirmed_manually'])) {
            throw ValidationException::withMessages(['payment_confirmed_manually' => 'Konfirmasi QRIS statis diperlukan.']);
        }

        return DB::transaction(function () use ($data, $cashierName): Transaction {
            if ($existing = Transaction::where('client_transaction_id', $data['client_transaction_id'])->first()) {
                return $existing;
            }
            $transaction = Transaction::create([
                'receipt_number' => $data['receipt_number'], 'payment_method' => $data['payment_method'],
                'total_amount' => $data['total_amount'], 'total_hpp' => $data['total_hpp'], 'paid_amount' => $data['paid_amount'],
                'change_amount' => $data['change_amount'], 'cashier_name' => $cashierName ?: 'Offline Kasir',
                'sync_status' => 'SYNCED', 'client_transaction_id' => $data['client_transaction_id'],
                'payment_confirmed_manually' => (bool) ($data['payment_confirmed_manually'] ?? false),
            ]);
            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (! $product) {
                    throw ValidationException::withMessages(['items' => 'Produk tidak ditemukan.']);
                }
                if ((float) $product->current_stock < (int) $item['quantity']) {
                    throw ValidationException::withMessages(['items' => "Stok {$product->name} tidak mencukupi."]);
                }
                $product->decrement('current_stock', (int) $item['quantity']);
                TransactionItem::create(['transaction_id' => $transaction->id, 'product_id' => $product->id, 'product_name' => $item['product_name'], 'unit_price' => $item['unit_price'], 'unit_hpp' => $item['unit_hpp'], 'quantity' => $item['quantity'], 'subtotal' => $item['subtotal']]);
            }

            return $transaction->load('items');
        });
    }
}

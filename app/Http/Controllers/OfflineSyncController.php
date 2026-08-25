<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\OfflineTransactionSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfflineSyncController extends Controller
{
    public function transactions(Request $request, OfflineTransactionSync $sync)
    {
        abort_unless($request->user()?->hasPermission('POS_ACCESS'), 403);
        $results = [];
        foreach ($request->validate(['items' => 'required|array|max:50'])['items'] as $item) {
            try {
                $tx = $sync->sync($item, $request->user()->name);
                $results[] = ['client_transaction_id' => $item['client_transaction_id'], 'status' => 'SYNCED', 'id' => $tx->id];
            } catch (\Throwable $e) {
                $results[] = ['client_transaction_id' => $item['client_transaction_id'] ?? null, 'status' => 'ERROR', 'error' => $e->getMessage()];
            }
        }

        return response()->json(['results' => $results]);
    }

    public function expenses(Request $request)
    {
        abort_unless($request->user()?->hasPermission('MANAGE_EXPENSES'), 403);
        $results = [];
        foreach ($request->validate(['items' => 'required|array|max:50'])['items'] as $item) {
            try {
                $data = validator($item, ['client_expense_id' => 'required|uuid', 'title' => 'required|string|max:255', 'amount' => 'required|numeric|min:1', 'category' => 'required|string|max:100', 'expense_date' => 'required', 'notes' => 'nullable|string'])->validate();
                $expense = DB::transaction(fn () => Expense::firstOrCreate(['client_expense_id' => $data['client_expense_id']], $data + ['sync_status' => 'SYNCED']));
                $results[] = ['client_expense_id' => $data['client_expense_id'], 'status' => 'SYNCED', 'id' => $expense->id];
            } catch (\Throwable $e) {
                $results[] = ['client_expense_id' => $item['client_expense_id'] ?? null, 'status' => 'ERROR', 'error' => $e->getMessage()];
            }
        }

        return response()->json(['results' => $results]);
    }
}

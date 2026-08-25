<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyProgram;
use App\Models\Product;
use App\Models\SyncDevice;
use App\Models\SyncQueue;
use App\Services\SyncQueueProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    public function registerDevice(Request $request)
    {
        $data = $request->validate(['device_id' => 'required|uuid', 'device_name' => 'nullable|string|max:120', 'platform' => 'required|in:web,mobile_web,android,ios,desktop']);
        $device = SyncDevice::updateOrCreate(['id' => $data['device_id'], 'user_id' => $request->user()->id], $data);

        return response()->json(['device_id' => $device->id, 'cursor' => $device->last_cursor]);
    }

    public function push(Request $request, SyncQueueProcessor $processor)
    {
        $data = $request->validate(['device_id' => 'required|uuid', 'operations' => 'required|array|max:100', 'operations.*.id' => 'required|uuid', 'operations.*.operation' => 'required|string|max:80', 'operations.*.entity_type' => 'required|string|max:80', 'operations.*.entity_id' => 'nullable|uuid', 'operations.*.payload' => 'required|array']);
        $device = SyncDevice::whereKey($data['device_id'])->where('user_id', $request->user()->id)->firstOrFail();
        $results = [];
        foreach ($data['operations'] as $op) {
            $row = DB::transaction(fn () => SyncQueue::firstOrCreate(['id' => $op['id']], ['device_id' => $device->id, 'operation' => $op['operation'], 'entity_type' => $op['entity_type'], 'entity_id' => $op['entity_id'] ?? null, 'payload' => $op['payload'], 'status' => 'PENDING', 'attempts' => 0]));
            $results[] = ['id' => $row->id, 'status' => $processor->process($row)];
        }
        $device->update(['last_synced_at' => now()]);

        return response()->json(['results' => $results, 'cursor' => now()->toISOString()]);
    }

    public function pull(Request $request)
    {
        $data = $request->validate(['device_id' => 'required|uuid', 'cursor' => 'nullable|date']);
        $device = SyncDevice::whereKey($data['device_id'])->where('user_id', $request->user()->id)->firstOrFail();
        $cursor = $data['cursor'] ?? $device->last_cursor;
        $products = Product::query()->when($cursor, fn ($q) => $q->where('updated_at', '>', $cursor))->get()->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'price' => $p->price, 'hpp' => $p->hpp, 'current_stock' => $p->current_stock, 'is_active' => $p->is_active, 'updated_at' => $p->updated_at?->toISOString()])->values();
        $programs = LoyaltyProgram::query()->when($cursor, fn ($q) => $q->where('updated_at', '>', $cursor))->get()->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'target_stamps' => $p->target_stamps, 'min_transaction' => $p->min_transaction, 'is_active' => $p->is_active, 'updated_at' => $p->updated_at?->toISOString()])->values();
        $nextCursor = now()->toISOString();
        $device->update(['last_cursor' => $nextCursor, 'last_synced_at' => now()]);

        return response()->json(['cursor' => $nextCursor, 'changes' => ['products' => $products, 'loyalty_programs' => $programs], 'device_id' => $device->id]);
    }
}

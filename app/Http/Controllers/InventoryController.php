<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
class InventoryController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {

        $query = InventoryItem::with(['creator', 'department']);
        // Optionally include movements when frontend requests it (for metrics like turnover)
        if ($request->query('with_movements')) {
            $query->with('movements');
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('low_stock')) {
            $query->whereRaw('stock_quantity <= reorder_level');
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'maximum_level' => 'nullable|integer|min:0',
            'unit_of_measure' => 'required|string',
            'unit_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $item = InventoryItem::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'status' => 'in_stock',
        ]);

        $item->updateStatus();
        $item->save();

        $this->auditService->log($request->user(), 'created', 'inventory', $item);

        return response()->json($item->fresh()->load(['creator', 'department', 'movements']), 201);
    }

    public function show($id)
    {
        $inventoryItem = InventoryItem::find($id);
        return response()->json($inventoryItem->load(['creator', 'department', 'movements.performer']));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'reorder_level' => 'sometimes|integer|min:0',
            'maximum_level' => 'nullable|integer|min:0',
            'unit_of_measure' => 'sometimes|string',
            'unit_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
        ]);

    $inventoryItem = InventoryItem::find($id);
    $oldValues = $inventoryItem->toArray();
    $inventoryItem->update($validated);
    // ensure status recalculation persisted
    $inventoryItem->updateStatus();
    $inventoryItem->save();

        $this->auditService->log(
            $request->user(),
            'updated',
            'inventory',
            $inventoryItem,
            $oldValues,
            $validated
        );

        return response()->json($inventoryItem->fresh()->load(['department']));
    }

    public function adjustStock(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:in,out,adjustment',
            'reason' => 'required|string',
        ]);

        $inventoryItem->adjustStock(
            $validated['quantity'],
            $validated['type'],
            $validated['reason'],
            $request->user()->id
        );

        $this->auditService->log(
            $request->user(),
            'stock_adjusted',
            'inventory',
            $inventoryItem
        );

        return response()->json($inventoryItem->fresh());
    }

    public function lowStockAlert()
    {
        return response()->json(
            InventoryItem::whereRaw('stock_quantity <= reorder_level')->get()
        );
    }

    public function destroy(Request $request, $id)
    {
        $item = InventoryItem::find($id);
        $item->forceDelete();
        // $this->authorize('delete', $inventoryItem);

        // $oldValues = $inventoryItem->toArray();

        // // Support soft delete by default. If client requests permanent deletion
        // // (e.g. ?force=1) or user is allowed to force delete, perform a forceDelete.
        // $force = (bool) $request->query('force', false);

        // if ($force) {
        //     $inventoryItem->forceDelete();
        //     $deleted = true;
        // } else {
        //     $inventoryItem->delete();

        //     // After delete(), refresh from DB (including trashed) to be sure we observe deleted_at
        //     $idToCheck = $oldValues['id'] ?? $inventoryItem->id ?? null;
        //     $fresh = null;
        //     if ($idToCheck) {
        //         $fresh = InventoryItem::withTrashed()->find($idToCheck);
        //         $deleted = $fresh ? (bool) $fresh->trashed() : false;
        //     } else {
        //         // log unexpected missing id for debugging
        //         Log::warning('InventoryController::destroy - no id available on inventory item', ['oldValues' => $oldValues, 'inventoryItem' => $inventoryItem->toArray()]);
        //         $deleted = false;
        //     }
        // }

        // $this->auditService->log(
        //     $request->user(),
        //     'deleted',
        //     'inventory',
        //     $inventoryItem,
        //     $oldValues,
        //     null
        // );

        // $idToReturn = $oldValues['id'] ?? $inventoryItem->id ?? null;

        // $response = [
        //     'message' => 'Inventory item deleted successfully',
        //     'id' => $idToReturn,
        //     'trashed' => $deleted,
        //     'force' => $force,
        //     'deleted_permanently' => $force,
        // ];

        // // Include debug information when app is in debug mode to help trace why id might be null
        // if (config('app.debug')) {
        //     $response['debug'] = [
        //         'oldValues' => $oldValues,
        //         'inventoryItem' => $inventoryItem->toArray(),
        //         'fresh_lookup' => isset($fresh) && $fresh ? $fresh->toArray() : null,
        //     ];
        // }

        return response()->json('success');
    }
}

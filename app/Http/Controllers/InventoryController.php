<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
        if (!$inventoryItem) {
            return response()->json(['message' => 'Inventory item not found'], 404);
        }

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
    if (!$inventoryItem) {
        return response()->json(['message' => 'Inventory item not found'], 404);
    }

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

    public function movements(InventoryItem $inventoryItem)
    {
        return response()->json(
            $inventoryItem->movements()->with('performer')->latest('movement_date')->paginate(20)
        );
    }

    public function destroy(Request $request, $id)
    {
        $item = InventoryItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Inventory item not found'], 404);
        }

        $this->authorize('delete', $item);

        $oldValues = $item->toArray();
        $item->delete();

        $this->auditService->log(
            $request->user(),
            'deleted',
            'inventory',
            $item,
            $oldValues,
            null
        );

        return response()->json(['message' => 'Inventory item deleted successfully', 'id' => $item->id]);
    }
}

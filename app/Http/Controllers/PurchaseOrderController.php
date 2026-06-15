<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Http\Resources\PurchaseOrderResource;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PurchaseOrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        $query = PurchaseOrder::with(['requester', 'approver', 'department']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor')) {
            $query->where('vendor_name', 'like', '%' . $request->vendor . '%');
        }

        return PurchaseOrderResource::collection($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseOrder::class);

        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'line_items' => 'nullable|array',
            'line_items.*.name' => 'required_with:line_items|string',
            'line_items.*.quantity' => 'required_with:line_items|numeric|min:0',
            'line_items.*.unit_price' => 'required_with:line_items|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $order = PurchaseOrder::create([
            ...$validated,
            'requested_by' => $request->user()->id,
            'status' => 'pending',
        ]);

        $this->auditService->log($request->user(), 'created', 'procurement', $order);

        return new PurchaseOrderResource($order->load(['requester', 'department']));
    }

    public function show(PurchaseOrder $procurement)
    {
        $this->authorize('view', $procurement);

        return new PurchaseOrderResource(
            $procurement->load(['requester', 'approver', 'department'])
        );
    }

    public function update(Request $request, PurchaseOrder $procurement)
    {
        $this->authorize('update', $procurement);

        $validated = $request->validate([
            'vendor_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'line_items' => 'nullable|array',
            'total_amount' => 'sometimes|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $procurement->toArray();
        $procurement->update($validated);

        $this->auditService->log(
            $request->user(),
            'updated',
            'procurement',
            $procurement,
            $oldValues,
            $validated
        );

        return new PurchaseOrderResource($procurement->fresh(['requester', 'department']));
    }

    public function destroy(Request $request, PurchaseOrder $procurement)
    {
        $this->authorize('delete', $procurement);

        $procurement->update(['status' => 'cancelled']);
        $procurement->delete();

        $this->auditService->log($request->user(), 'deleted', 'procurement', $procurement);

        return response()->json(['message' => 'Purchase order cancelled']);
    }

    public function approve(Request $request, PurchaseOrder $procurement)
    {
        $this->authorize('approve', $procurement);

        if ($procurement->status !== 'pending') {
            return response()->json(['message' => 'Purchase order already processed'], 400);
        }

        $procurement->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $this->auditService->log($request->user(), 'approved', 'procurement', $procurement);

        return new PurchaseOrderResource($procurement->fresh(['requester', 'approver']));
    }

    public function reject(Request $request, PurchaseOrder $procurement)
    {
        $this->authorize('approve', $procurement);

        if ($procurement->status !== 'pending') {
            return response()->json(['message' => 'Purchase order already processed'], 400);
        }

        $procurement->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $this->auditService->log($request->user(), 'rejected', 'procurement', $procurement);

        return new PurchaseOrderResource($procurement->fresh(['requester', 'approver']));
    }

    public function fulfill(Request $request, PurchaseOrder $procurement)
    {
        $this->authorize('approve', $procurement);

        if ($procurement->status !== 'approved') {
            return response()->json(['message' => 'Only approved orders can be fulfilled'], 400);
        }

        $procurement->update(['status' => 'fulfilled']);

        $this->auditService->log($request->user(), 'fulfilled', 'procurement', $procurement);

        return new PurchaseOrderResource($procurement->fresh());
    }

    public function statistics()
    {
        return response()->json([
            'total' => PurchaseOrder::count(),
            'pending' => PurchaseOrder::where('status', 'pending')->count(),
            'approved' => PurchaseOrder::where('status', 'approved')->count(),
            'fulfilled' => PurchaseOrder::where('status', 'fulfilled')->count(),
            'total_value' => PurchaseOrder::whereIn('status', ['approved', 'fulfilled'])->sum('total_amount'),
        ]);
    }
}

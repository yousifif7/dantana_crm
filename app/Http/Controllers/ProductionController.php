<?php

namespace App\Http\Controllers;

use App\Models\ProductionRecord;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $query = ProductionRecord::with(['creator', 'approver']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date')) {
            $query->where('production_date', '>=', $request->start_date);
        }

        return response()->json($query->latest('production_date')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'production_date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'efficiency_percentage' => 'required|integer|min:0|max:100',
            'downtime_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $record = ProductionRecord::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'status' => 'pending',
        ]);

        $this->auditService->log($request->user(), 'created', 'production', $record);

        return response()->json($record, 201);
    }

    public function show(ProductionRecord $productionRecord)
    {
        return response()->json($productionRecord->load(['creator', 'approver']));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'production_date' => 'sometimes|date',
            'quantity' => 'sometimes|numeric|min:0',
            'efficiency_percentage' => 'sometimes|integer|min:0|max:100',
            'downtime_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $productionRecord = ProductionRecord::find($id);
        $productionRecord->update($validated);

        $this->auditService->log($request->user(), 'updated', 'production', $productionRecord);

        return response()->json($productionRecord);
    }

    public function approve(Request $request, ProductionRecord $productionRecord)
    {
        $productionRecord->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
        ]);

        $this->auditService->log($request->user(), 'approved', 'production', $productionRecord);

        return response()->json($productionRecord);
    }

    public function statistics(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());

        $stats = ProductionRecord::where('status', 'approved')
            ->whereBetween('production_date', [$startDate, $endDate])
            ->selectRaw('
                SUM(quantity) as total_production,
                AVG(efficiency_percentage) as avg_efficiency,
                SUM(downtime_hours) as total_downtime,
                COUNT(*) as total_batches
            ')
            ->first();

        return response()->json($stats);
    }

    public function destroy($id){
        $product = ProductionRecord::find($id);
        $product->delete();
        return response()->json('Production deleted successfully!');
    }
}

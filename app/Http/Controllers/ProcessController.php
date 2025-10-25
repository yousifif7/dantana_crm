<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $query = Process::with(['assignedUser', 'creator']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'priority' => 'nullable|integer|min:1|max:5',
        ]);

        $process = Process::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'status' => 'pending',
        ]);

        $this->auditService->log($request->user(), 'created', 'process', $process);

        return response()->json($process, 201);
    }

    public function update(Request $request, Process $process)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
            'due_date' => 'sometimes|date',
            'priority' => 'nullable|integer|min:1|max:5',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $process->update($validated);

        $this->auditService->log($request->user(), 'updated', 'process', $process);

        return response()->json($process);
    }

    /**
     * Return a single process with its relations for editing/viewing.
     */
    public function show(Request $request, Process $process)
    {
        $process->load(['assignedUser.role', 'creator']);

        // For convenience, attach a resolved assigned_to_name field used by the frontend
        $process->assigned_to_name = $process->assignedUser ? ($process->assignedUser->first_name . ' ' . $process->assignedUser->last_name) : null;

        return response()->json($process);
    }

    public function overdueProcesses()
    {
        return response()->json(Process::overdue()->with('assignedUser')->get());
    }

    /**
     * Delete a process (soft delete).
     */
    public function destroy(Request $request, Process $process)
    {
        $this->auditService->log($request->user(), 'deleted', 'process', $process);
        $process->delete();
        return response()->json(['message' => 'Process deleted']);
    }
}
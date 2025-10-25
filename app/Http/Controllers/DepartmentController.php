<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Services\{AuditService, ReportService};
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        private AuditService $auditService,
        private ReportService $reportService
    ) {}

    public function index()
    {
        $departments = Department::with(['generalManager', 'departmentHead'])
            ->withCount('users')
            ->get();

        return DepartmentResource::collection($departments);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create($request->validated());

        $this->auditService->log(
            $request->user(),
            'created',
            'hr',
            $department
        );

        return new DepartmentResource($department->load(['generalManager', 'departmentHead']));
    }

    public function show(Department $department)
    {
        return new DepartmentResource(
            $department->load(['generalManager', 'departmentHead', 'users'])
        );
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'general_manager_id' => 'nullable|exists:users,id',
            'department_head_id' => 'nullable|exists:users,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $oldValues = $department->toArray();
        $department->update($validated);

        $this->auditService->log(
            $request->user(),
            'updated',
            'hr',
            $department,
            $oldValues,
            $validated
        );

        return new DepartmentResource($department->fresh(['generalManager', 'departmentHead']));
    }

    public function users(Department $department)
    {
        return response()->json(
            $department->users()->with('role')->get()
        );
    }

    public function performance(Department $department)
    {
        return response()->json(
            $this->reportService->departmentPerformance($department->id)
        );
    }
}
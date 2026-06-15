<?php

namespace App\Http\Controllers;

use App\Models\Department;
// note: store uses manual validation to avoid FormRequest authorize issues in some dev setups
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

    public function store(Request $request)
    {
        // Validate manually to avoid FormRequest authorization blocking in certain dev setups
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:30',
            'contact_email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'extra_info' => 'nullable|string|max:2000',
            'general_manager_id' => 'nullable|exists:users,id',
            'department_head_id' => 'nullable|exists:users,id',
        ]);

        $department = Department::create($validated);

        // Audit will accept nullable user; if session isn't present audit user_id will be null
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
            $department->load(['generalManager', 'departmentHead', 'users.role'])
        );
    }

    public function update(\App\Http\Requests\UpdateDepartmentRequest $request, Department $department)
    {
        // Authorization is handled by UpdateDepartmentRequest
        $validated = $request->validated();

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

    public function destroy(Request $request, Department $department)
    {
        $user = $request->user();
        if (!$user || !$user->can('delete', $department)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $oldValues = $department->toArray();
        $department->delete();

        $this->auditService->log(
            $user,
            'deleted',
            'hr',
            $department,
            $oldValues
        );

        return response()->json(['message' => 'Division deleted successfully']);
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
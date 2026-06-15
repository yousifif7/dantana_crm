<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\{StoreUserRequest, UpdateUserRequest};
use App\Http\Resources\UserResource;
use App\Services\{AuditService, AuthService};
use App\Events\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function __construct(
        private AuditService $auditService,
        private AuthService $authService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['role', 'department', 'supervisor']);

        // Filter by department
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by role
        if ($request->has('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate($request->input('per_page', 15));

        $this->auditService->log(
            $request->user(),
            'viewed',
            'hr',
            null
        );

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request)
    {
        $temporaryPassword = $request->password;
        $employeeId = $request->filled('employee_id')
            ? $request->employee_id
            : $this->generateEmployeeId();

        $user = User::create([
            'employee_id' => $employeeId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'reports_to' => $request->reports_to,
            'hire_date' => $request->hire_date,
            'age' => $request->age,
            'is_active' => true,
        ]);

        $this->auditService->log(
            $request->user(),
            'created',
            'hr',
            $user,
            null,
            $request->validated()
        );

        // Fire event to send welcome email
        event(new UserCreated($user, $temporaryPassword));

        return new UserResource($user->load(['role', 'department']));
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $user)
    {
        $this->authorize('view', $user);

        $user->load([
            'role',
            'department',
            'supervisor',
            'subordinates.role',
            'assignedProcesses' => function ($query) {
                $query->where('status', '!=', 'completed')->latest();
            }
        ]);

        return new UserResource($user);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $oldValues = $user->toArray();

        $user->update($request->validated());

        $this->auditService->log(
            $request->user(),
            'updated',
            'hr',
            $user,
            $oldValues,
            $request->validated()
        );

        return new UserResource($user->fresh(['role', 'department']));
    }

    /**
     * Remove the specified user (soft delete).
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorize('delete', $user);

        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot delete your own account'
            ], 400);
        }

        $oldValues = $user->toArray();
        $user->delete();

        $this->auditService->log(
            $request->user(),
            'deleted',
            'hr',
            $user,
            $oldValues,
            null
        );

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Activate a user account.
     */
    public function activate(Request $request, User $user)
    {
        $this->authorize('update', $user);

        if ($user->is_active) {
            return response()->json([
                'message' => 'User is already active'
            ], 400);
        }

        $user->update(['is_active' => true]);

        $this->auditService->log(
            $request->user(),
            'activated',
            'hr',
            $user
        );

        return response()->json([
            'message' => 'User activated successfully',
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Deactivate a user account.
     */
    public function deactivate(Request $request, User $user)
    {
        $this->authorize('update', $user);

        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot deactivate your own account'
            ], 400);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'User is already inactive'
            ], 400);
        }

        $user->update(['is_active' => false]);

        // Revoke all tokens
        $user->tokens()->delete();

        $this->auditService->log(
            $request->user(),
            'deactivated',
            'hr',
            $user
        );

        return response()->json([
            'message' => 'User deactivated successfully',
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'send_email' => 'nullable|boolean',
        ]);

        // Generate new temporary password
        $temporaryPassword = Str::random(12);
        
        $user->update([
            'password' => Hash::make($temporaryPassword)
        ]);

        // Revoke all existing tokens
        $user->tokens()->delete();

        $this->auditService->log(
            $request->user(),
            'password_reset',
            'hr',
            $user
        );

        // Optionally send email with new password
        if ($request->boolean('send_email', true)) {
            event(new UserCreated($user, $temporaryPassword));
        }

        return response()->json([
            'message' => 'Password reset successfully',
            'temporary_password' => $temporaryPassword,
            'note' => 'Please provide this password to the user securely'
        ]);
    }

    /**
     * Change own password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
                'errors' => [
                    'current_password' => ['Current password is incorrect']
                ]
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        $this->auditService->log(
            $user,
            'password_changed',
            'auth',
            null
        );

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Update own profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $oldValues = $user->only(array_keys($validated));
        $user->update($validated);

        $this->auditService->log(
            $user,
            'profile_updated',
            'hr',
            $user,
            $oldValues,
            $validated
        );

        return new UserResource($user->fresh(['role', 'department']));
    }

    /**
     * Get user statistics.
     */
    public function statistics(Request $request, User $user)
    {
        $this->authorize('view', $user);

        $stats = [
            'assigned_processes' => $user->assignedProcesses()->count(),
            'completed_processes' => $user->assignedProcesses()
                ->where('status', 'completed')
                ->count(),
            'overdue_processes' => $user->assignedProcesses()
                ->where('status', '!=', 'completed')
                ->where('due_date', '<', now())
                ->count(),
            'attendance_rate' => $this->calculateAttendanceRate($user),
            'subordinates_count' => $user->subordinates()->count(),
            'created_transactions' => $user->transactions()->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get user's subordinates.
     */
    public function subordinates(Request $request, User $user)
    {
        $this->authorize('view', $user);

        $subordinates = $user->subordinates()
            ->with(['role', 'department'])
            ->where('is_active', true)
            ->get();

        return UserResource::collection($subordinates);
    }

    /**
     * Get users by department.
     */
    public function byDepartment(Request $request, int $departmentId)
    {
        $users = User::with(['role', 'supervisor'])
            ->where('department_id', $departmentId)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        return UserResource::collection($users);
    }

    /**
     * Get users by role.
     */
    public function byRole(Request $request, int $roleId)
    {
        $users = User::with(['department', 'supervisor'])
            ->where('role_id', $roleId)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        return UserResource::collection($users);
    }

    /**
     * Bulk activate users.
     */
    public function bulkActivate(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|exists:users,id',
        ]);

        $count = User::whereIn('id', $request->user_ids)
            ->update(['is_active' => true]);

        $this->auditService->log(
            $request->user(),
            'bulk_activated',
            'hr',
            null,
            null,
            ['user_ids' => $request->user_ids, 'count' => $count]
        );

        return response()->json([
            'message' => "{$count} users activated successfully",
            'count' => $count
        ]);
    }

    /**
     * Bulk deactivate users.
     */
    public function bulkDeactivate(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|exists:users,id',
        ]);

        // Prevent deactivating self
        if (in_array($request->user()->id, $request->user_ids)) {
            return response()->json([
                'message' => 'You cannot deactivate your own account'
            ], 400);
        }

        $count = User::whereIn('id', $request->user_ids)
            ->update(['is_active' => false]);

        // Revoke tokens for deactivated users
        User::whereIn('id', $request->user_ids)->get()->each(function ($user) {
            $user->tokens()->delete();
        });

        $this->auditService->log(
            $request->user(),
            'bulk_deactivated',
            'hr',
            null,
            null,
            ['user_ids' => $request->user_ids, 'count' => $count]
        );

        return response()->json([
            'message' => "{$count} users deactivated successfully",
            'count' => $count
        ]);
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $users = User::with(['role', 'department'])
            ->where('is_active', true)
            ->get();

        $csvData = "Employee ID,First Name,Last Name,Email,Phone,Role,Department,Hire Date\n";
        
        foreach ($users as $user) {
            $csvData .= implode(',', [
                $user->employee_id,
                $user->first_name,
                $user->last_name,
                $user->email,
                $user->phone ?? '',
                $user->role->display_name,
                $user->department?->name ?? '',
                $user->hire_date?->format('Y-m-d') ?? '',
            ]) . "\n";
        }

        $this->auditService->log(
            $request->user(),
            'exported',
            'hr',
            null
        );

        return response($csvData, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="users_export_' . date('Y-m-d') . '.csv"');
    }

    /**
     * Get available supervisors for assignment.
     */
    public function availableSupervisors(Request $request)
    {
        $supervisors = User::whereHas('role', function ($query) {
            $query->whereIn('name', [
                'department_head',
                'general_manager',
                'md'
            ]);
        })
        ->where('is_active', true)
        ->with(['role', 'department'])
        ->orderBy('first_name')
        ->get();

        return UserResource::collection($supervisors);
    }

    /**
     * Generate unique employee ID.
     */
    private function generateEmployeeId(): string
    {
        $maxNum = User::withTrashed()
            ->where('employee_id', 'like', 'EMP%')
            ->pluck('employee_id')
            ->map(fn ($id) => (int) substr($id, 3))
            ->max() ?? 0;

        return 'EMP' . str_pad($maxNum + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate attendance rate for user.
     */
    private function calculateAttendanceRate(User $user): float
    {
        $thirtyDaysAgo = now()->subDays(30);
        
        $totalDays = $user->attendanceRecords()
            ->where('attendance_date', '>=', $thirtyDaysAgo)
            ->count();

        $presentDays = $user->attendanceRecords()
            ->where('attendance_date', '>=', $thirtyDaysAgo)
            ->where('status', 'present')
            ->count();

        if ($totalDays === 0) {
            return 0;
        }

        return round(($presentDays / $totalDays) * 100, 2);
    }
}

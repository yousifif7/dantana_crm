<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\InventoryItem;
use App\Models\ProductionRecord;
use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Department;
use App\Models\User as UserModel;
use App\Services\AuthService;

class DashboardController extends Controller
{
    public function __construct(private AuthService $authService) {}
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Role-based dashboard data
        switch ($user->role->name) {
            case 'chairman':
                return $this->chairmanDashboard();
            case 'md':
                return $this->mdDashboard();
            case 'general_manager':
                return $this->gmDashboard($user);
            case 'department_head':
                return $this->departmentHeadDashboard($user);
            default:
                return $this->staffDashboard($user);
        }
    }

    /**
     * Return general dashboard statistics used by the frontend charts and widgets.
     */
    public function statistics(Request $request)
    {
        // Transactions
        $totalTransactions = Transaction::count();
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $approvedTransactions = Transaction::where('status', 'approved')->count();

        // Inventory
        $totalInventoryItems = InventoryItem::count();
        // low stock defined as stock_quantity <= reorder_level (ReportService uses same raw)
        $lowStockCount = InventoryItem::whereRaw('stock_quantity <= reorder_level')->count();

        // Production
        $totalProduction = ProductionRecord::count();
        $pendingProduction = ProductionRecord::where('status', 'pending')->count();

        // Processes
        $overdueProcesses = Process::overdue()->count();
        $pendingProcurement = PurchaseOrder::where('status', 'pending')->count();

        // Users & Departments
        $totalUsers = User::count();
        $totalDepartments = DB::table('departments')->count();

        return response()->json([
            'transactions' => [
                'total' => $totalTransactions,
                'pending' => $pendingTransactions,
                'approved' => $approvedTransactions,
            ],
            'inventory' => [
                'total_items' => $totalInventoryItems,
                'low_stock' => $lowStockCount,
            ],
            'production' => [
                'total' => $totalProduction,
                'pending' => $pendingProduction,
            ],
            'processes' => [
                'overdue' => $overdueProcesses,
            ],
            'procurement' => [
                'pending' => $pendingProcurement,
                'total_value' => PurchaseOrder::whereIn('status', ['approved', 'fulfilled'])->sum('total_amount'),
            ],
            'counts' => [
                'users' => $totalUsers,
                'departments' => $totalDepartments,
            ],
        ]);
    }

    private function chairmanDashboard()
    {
        return response()->json([
            'metrics' => [
                'total_revenue' => Transaction::revenue()->approved()->sum('amount'),
                'total_expenses' => Transaction::expense()->approved()->sum('amount'),
                'departments' => DB::table('departments')->where('is_active', true)->count(),
                'total_employees' => User::where('is_active', true)->count(),
            ],
            'pending_approvals' => [
                'transactions' => Transaction::where('status', 'pending')->count(),
                'production' => ProductionRecord::where('status', 'pending')->count(),
            ],
        ]);
    }

    private function mdDashboard()
    {
        $revenue = Transaction::revenue()->approved()
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        $expenses = Transaction::expense()->approved()
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        return response()->json([
            'metrics' => [
                'total_revenue' => Transaction::revenue()->approved()->sum('amount'),
                'total_expenses' => Transaction::expense()->approved()->sum('amount'),
                'net_profit' => Transaction::revenue()->approved()->sum('amount') - 
                               Transaction::expense()->approved()->sum('amount'),
                'profit_margin' => $this->calculateProfitMargin(),
            ],
            'charts' => [
                'revenue' => $revenue,
                'expenses' => $expenses,
                'expense_breakdown' => $this->getExpenseBreakdown(),
            ],
            'recent_transactions' => Transaction::with('creator')->latest()->limit(10)->get(),
        ]);
    }

    private function gmDashboard($user)
    {
        return response()->json([
            'department' => $user->department->load('users'),
            'metrics' => [
                'employees' => $user->department->users()->count(),
                'attendance_rate' => $this->calculateDepartmentAttendance($user->department_id),
                'pending_tasks' => DB::table('processes')
                    ->whereIn('assigned_to', $user->department->users()->pluck('id'))
                    ->where('status', '!=', 'completed')
                    ->count(),
            ],
        ]);
    }

    private function departmentHeadDashboard($user)
    {
        return response()->json([
            'team_metrics' => [
                'team_size' => $user->subordinates()->count(),
                'active_processes' => $user->subordinates()->withCount('assignedProcesses')->get(),
            ],
        ]);
    }

    private function staffDashboard($user)
    {
        return response()->json([
            'my_tasks' => $user->assignedProcesses()->where('status', '!=', 'completed')->get(),
            'recent_attendance' => $user->attendanceRecords()->latest()->limit(7)->get(),
        ]);
    }

    private function calculateProfitMargin()
    {
        $revenue = Transaction::revenue()->approved()->sum('amount');
        $expenses = Transaction::expense()->approved()->sum('amount');
        
        if ($revenue == 0) return 0;
        
        return round((($revenue - $expenses) / $revenue) * 100, 2);
    }

    private function getExpenseBreakdown()
    {
        return Transaction::expense()->approved()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
    }

    private function calculateDepartmentAttendance($departmentId)
    {
        $users = User::where('department_id', $departmentId)->pluck('id');
        $totalDays = 30;
        $presentDays = DB::table('attendance_records')
            ->whereIn('user_id', $users)
            ->where('status', 'present')
            ->where('attendance_date', '>=', now()->subDays(30))
            ->count();

        $expectedDays = $users->count() * $totalDays;
        return $expectedDays > 0 ? round(($presentDays / $expectedDays) * 100, 2) : 0;
    }

    /**
     * Handle the Add Employee form submitted from the Blade dashboard.
     * This is the web POST route used by the 'Add Employee' modal.
     */
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['nullable', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['required', 'exists:roles,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        try {
            $user = UserModel::create([
                'employee_id' => $data['employee_id'] ?? $this->authService->generateEmployeeId(),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $data['role_id'],
                'department_id' => $data['department_id'] ?? null,
                'is_active' => true,
            ]);

            return redirect()->route('dashboard')->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            // On failure, redirect back with errors (old input preserved by Laravel automatically)
            return redirect()->route('dashboard')->withErrors(['failed' => 'Failed to create user: ' . $e->getMessage()])->withInput();
        }
    }
}

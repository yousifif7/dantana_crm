<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\ProductionRecord;
use App\Models\InventoryItem;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function financialReport(string $startDate, string $endDate)
    {
        $revenue = Transaction::revenue()
            ->approved()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $expenses = Transaction::expense()
            ->approved()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $netProfit = $revenue - $expenses;
        $profitMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        $expensesByCategory = Transaction::expense()
            ->approved()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'revenue' => $revenue,
            'expenses' => $expenses,
            'net_profit' => $netProfit,
            'profit_margin' => round($profitMargin, 2),
            'expenses_by_category' => $expensesByCategory,
        ];
    }

    public function productionReport(string $startDate, string $endDate)
    {
        $records = ProductionRecord::where('status', 'approved')
            ->whereBetween('production_date', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_batches,
                SUM(quantity) as total_quantity,
                AVG(efficiency_percentage) as avg_efficiency,
                SUM(downtime_hours) as total_downtime
            ')
            ->first();

        $dailyProduction = ProductionRecord::where('status', 'approved')
            ->whereBetween('production_date', [$startDate, $endDate])
            ->selectRaw('DATE(production_date) as date, SUM(quantity) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'summary' => $records,
            'daily_production' => $dailyProduction,
        ];
    }

    public function inventoryReport()
    {
        $totalValue = InventoryItem::selectRaw('SUM(stock_quantity * unit_price) as total')
            ->value('total');

        $lowStockItems = InventoryItem::whereRaw('stock_quantity <= reorder_level')->get();

        $stockByStatus = InventoryItem::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return [
            'total_items' => InventoryItem::count(),
            'total_value' => $totalValue,
            'low_stock_items' => $lowStockItems,
            'stock_by_status' => $stockByStatus,
        ];
    }

    public function attendanceReport(string $startDate, string $endDate, ?int $departmentId = null)
    {
        $query = AttendanceRecord::whereBetween('attendance_date', [$startDate, $endDate]);

        if ($departmentId) {
            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $summary = $query->selectRaw('
            status,
            COUNT(*) as count
        ')
        ->groupBy('status')
        ->get();

        $totalRecords = $summary->sum('count');
        $presentCount = $summary->where('status', 'present')->first()?->count ?? 0;
        $attendanceRate = $totalRecords > 0 ? ($presentCount / $totalRecords) * 100 : 0;

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'summary' => $summary,
            'attendance_rate' => round($attendanceRate, 2),
        ];
    }

    public function departmentPerformance(int $departmentId)
    {
        $department = \App\Models\Department::with(['users' => function ($query) {
            $query->where('is_active', true);
        }])->findOrFail($departmentId);

        $employeeCount = $department->users->count();

        $completedProcesses = \App\Models\Process::whereIn('assigned_to', $department->users->pluck('id'))
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subDays(30))
            ->count();

        $overdueProcesses = \App\Models\Process::whereIn('assigned_to', $department->users->pluck('id'))
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->count();

        $attendanceRate = AttendanceRecord::whereIn('user_id', $department->users->pluck('id'))
            ->where('attendance_date', '>=', now()->subDays(30))
            ->where('status', 'present')
            ->count();

        $expectedDays = $employeeCount * 30;
        $attendancePercentage = $expectedDays > 0 ? ($attendanceRate / $expectedDays) * 100 : 0;

        return [
            'department' => $department->only(['id', 'name', 'code']),
            'employee_count' => $employeeCount,
            'completed_processes' => $completedProcesses,
            'overdue_processes' => $overdueProcesses,
            'attendance_rate' => round($attendancePercentage, 2),
        ];
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EscalationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

use App\Http\Controllers\{
    AuthController,
    DashboardController,
    TransactionController,
    InventoryController,
    ProductionController,
    ProcessController,
    UserController,
    DepartmentController,
    AttendanceController,
    ReportController,
    NotificationController,
    AuditController,
    PurchaseOrderController
};

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware(['auth:sanctum', 'log.activity'])->group(function () {
    
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/2fa/enable', [AuthController::class, 'enableTwoFactor']);
    Route::post('/2fa/verify', [AuthController::class, 'verifyTwoFactor']);
    Route::post('/2fa/disable', [AuthController::class, 'disableTwoFactor']);
    
    // Dashboard - Role-based views
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    
    // Transactions (Finance Module) — statistics before resource to avoid route conflict
    Route::get('/transactions/summary/statistics', [TransactionController::class, 'statistics']);
    Route::apiResource('transactions', TransactionController::class);
    Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve']);
    Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject']);
    
    // Inventory Management
    Route::get('/inventory/alerts/low-stock', [InventoryController::class, 'lowStockAlert']);
    Route::get('/inventory/{inventoryItem}/movements', [InventoryController::class, 'movements']);
    Route::apiResource('inventory', InventoryController::class);
    Route::post('/inventory/{inventoryItem}/adjust', [InventoryController::class, 'adjustStock']);
    
    // Production Management
    Route::get('/production/summary/statistics', [ProductionController::class, 'statistics']);
    Route::apiResource('production', ProductionController::class);
    Route::post('/production/{productionRecord}/approve', [ProductionController::class, 'approve']);
    Route::post('/production/{productionRecord}/reject', [ProductionController::class, 'reject']);
    
    // Process Management
    Route::get('/processes/alerts/overdue', [ProcessController::class, 'overdueProcesses']);
    Route::post('/processes/{process}/complete', [ProcessController::class, 'complete']);
    Route::apiResource('processes', ProcessController::class);
    
    // User Management (HR Module)
    Route::apiResource('users', UserController::class);
    Route::post('/users/{user}/activate', [UserController::class, 'activate']);
    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate']);
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword']);
    
    // Department Management
    Route::apiResource('departments', DepartmentController::class);
    Route::get('/departments/{department}/users', [DepartmentController::class, 'users']);
    Route::get('/departments/{department}/performance', [DepartmentController::class, 'performance']);
    
    // Attendance
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/my-records', [AttendanceController::class, 'myRecords']);
    Route::get('/attendance/department/{department}', [AttendanceController::class, 'departmentAttendance']);
    Route::apiResource('attendance', AttendanceController::class);
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/financial', [ReportController::class, 'financial']);
        Route::get('/production', [ReportController::class, 'production']);
        Route::get('/inventory', [ReportController::class, 'inventory']);
        Route::get('/attendance', [ReportController::class, 'attendance']);
        Route::get('/department/{department}', [ReportController::class, 'department']);
        Route::get('/export/financial', [ReportController::class, 'exportFinancial']);
        Route::get('/export/production', [ReportController::class, 'exportProduction']);
    });
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    
    // Audit Logs
    Route::get('/audit-logs', [AuditController::class, 'index']);
    Route::get('/audit-logs/module/{module}', [AuditController::class, 'byModule']);
    Route::get('/audit-logs/user/{user}', [AuditController::class, 'byUser']);
    Route::get('/audit-logs/model/{type}/{id}', [AuditController::class, 'byModel']);
    
    // Escalations
    Route::get('/escalations', [EscalationController::class, 'index']);
    Route::get('/escalations/my-pending', [EscalationController::class, 'myPending']);
    Route::post('/escalations/{escalation}/resolve', [EscalationController::class, 'resolve']);
    
    // Procurement
    Route::get('/procurement/summary/statistics', [PurchaseOrderController::class, 'statistics']);
    Route::apiResource('procurement', PurchaseOrderController::class);
    Route::post('/procurement/{procurement}/approve', [PurchaseOrderController::class, 'approve']);
    Route::post('/procurement/{procurement}/reject', [PurchaseOrderController::class, 'reject']);
    Route::post('/procurement/{procurement}/fulfill', [PurchaseOrderController::class, 'fulfill']);
    
    // Roles & Permissions
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{role}', [RoleController::class, 'show']);
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'syncPermissions']);
});

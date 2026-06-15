<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Models\Role;
use App\Models\Department;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\ProductionRecord;
use App\Models\Transaction;

// Redirect root to the login page.
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard/{page?}', function (?string $page = null) {
    $slugToPageId = [
        'production' => 'oil-production',
        'food' => 'food-division',
        'processes' => 'process',
        'audit' => 'audit-logs',
    ];
    $activePageId = match (true) {
        $page === null || $page === '' => 'ai-dashboard',
        isset($slugToPageId[$page]) => $slugToPageId[$page],
        default => $page,
    };

    $roles = Role::orderBy('display_name')->get();
    $departments = Department::withCount('users')->with(['users.role'])->get();
    $users = User::where('is_active', true)->orderBy('first_name')->get();
    $metrics = [
        'user_count' => User::count(),
        'departments_count' => Department::count(),
        'inventory_items' => InventoryItem::count(),
        'production_records' => ProductionRecord::count(),
        'transactions_count' => Transaction::count(),
    ];

    return view('dashboard', compact('roles', 'departments', 'metrics', 'users', 'page', 'activePageId'));
})->where('page', '[a-z\-]+')->name('dashboard');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Protected web routes — require Sanctum bearer token (sent by dashboard apiFetch)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/dashboard/users', [DashboardController::class, 'storeUser'])->name('dashboard.users.store');
    Route::post('/dashboard/departments', [DepartmentController::class, 'store'])->name('dashboard.departments.store');
    Route::put('/dashboard/departments/{department}', [DepartmentController::class, 'update'])->name('dashboard.departments.update');
    Route::delete('/departments/delete/{department}', [DepartmentController::class, 'destroy']);
});

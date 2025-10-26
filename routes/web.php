<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Serve the SPA dashboard view for all web requests. The dashboard JS will call the
// authenticated API endpoints under /api/* to fetch data and handle login.
// Redirect root to the login page. The SPA dashboard is served at /dashboard after login.
Route::get('/', function () {
	return redirect()->route('login');
});

use App\Models\Role;
use App\Models\Department;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\ProductionRecord;
use App\Models\Transaction;
use App\Http\Controllers\DepartmentController;

Route::get('/dashboard', function () {
	// Provide server-side data required for the Blade add-employee form and
	// initial staff view so selects and counts are populated from the DB.
	$roles = Role::orderBy('display_name')->get();
	$departments = Department::withCount('users')->with('users')->get();
	$users = User::where('is_active', true)->orderBy('first_name')->get();
	$metrics = [
		'user_count' => User::count(),
		'departments_count' => Department::count(),
		'inventory_items' => InventoryItem::count(),
		'production_records' => ProductionRecord::count(),
		'transactions_count' => Transaction::count(),
	];

	return view('dashboard', compact('roles', 'departments', 'metrics', 'users'));
})->name('dashboard');

// Auth pages (client-side login/register that call API endpoints)
Route::get('/login', function () {
	return view('auth.login');
})->name('login');

Route::get('/register', function () {
	return view('auth.register');
})->name('register');

// Provide a web POST endpoint that the Blade Add Employee form can submit to.
// This keeps the existing Blade form working (it uses route('dashboard.users.store')).
Route::post('/dashboard/users', [DashboardController::class, 'storeUser'])->name('dashboard.users.store');

// Allow Blade to submit department creation from the dashboard (normal form submit)
Route::post('/dashboard/departments', [DepartmentController::class, 'store'])->name('dashboard.departments.store');
Route::put('/dashboard/departments/{department}', [DepartmentController::class, 'update'])->name('dashboard.departments.update');
// Route::delete('/dashboard/departments/{department}', [DepartmentController::class, 'destroy'])->name('dashboard.departments.destroy');
Route::delete('/departments/delete/{department}', [DepartmentController::class, 'destroy']);

// Temporary debug endpoint to inspect the authenticated user and role from the browser.
// Remove this route after debugging session/cookie issues.
use Illuminate\Support\Facades\Auth;

Route::get('/debug/whoami', function () {
	$user = Auth::user();
	if (! $user) return response()->json(['user' => null, 'message' => 'guest']);
	return response()->json(['user' => [
		'id' => $user->id,
		'email' => $user->email ?? null,
		'role' => $user->role->name ?? null,
	]]);
});


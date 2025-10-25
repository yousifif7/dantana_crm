<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Module access mapping based on role
        $moduleAccess = [
            'chairman' => ['all'],
            'md' => ['all'],
            'cfo' => ['finance'],
            'general_manager' => ['all'], // Within department
            'department_head' => ['all'], // Within department
            'hr_officer' => ['hr'],
            'procurement_officer' => ['procurement', 'inventory'],
            'operations_officer' => ['production', 'operations'],
            'executive' => ['assigned'],
            'officer' => ['assigned'],
            'support_staff' => ['attendance', 'tasks'],
        ];

        $allowedModules = $moduleAccess[$user->role->name] ?? [];

        if (!in_array('all', $allowedModules) && !in_array($module, $allowedModules)) {
            return response()->json(['message' => 'Module access denied'], 403);
        }

        return $next($request);
    }
}

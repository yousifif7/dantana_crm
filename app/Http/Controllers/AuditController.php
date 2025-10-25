<?php

namespace App\Http\Controllers;

use App\Models\{AuditLog, User};
use App\Http\Resources\AuditLogResource;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->has('module')) {
            $query->where('module', $request->module);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $logs = $query->latest()->paginate(50);

        return AuditLogResource::collection($logs);
    }

    public function byModule(Request $request, string $module)
    {
        $days = $request->input('days', 30);
        
        $logs = AuditLog::where('module', $module)
            ->where('created_at', '>=', now()->subDays($days))
            ->with('user')
            ->latest()
            ->paginate(50);

        return AuditLogResource::collection($logs);
    }

    public function byUser(Request $request, User $user)
    {
        $days = $request->input('days', 30);
        
        $logs = AuditLog::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->latest()
            ->paginate(50);

        return AuditLogResource::collection($logs);
    }

    public function byModel(Request $request, string $type, int $id)
    {
        $modelClass = 'App\\Models\\' . $type;
        
        if (!class_exists($modelClass)) {
            return response()->json(['message' => 'Invalid model type'], 400);
        }

        $logs = AuditLog::where('auditable_type', $modelClass)
            ->where('auditable_id', $id)
            ->with('user')
            ->latest()
            ->get();

        return AuditLogResource::collection($logs);
    }
}
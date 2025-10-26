<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public function log(
        ?User $user,
        string $action,
        string $module,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        // auditable_type is non-nullable in the schema (morphs), so ensure we always provide a string.
        // Use 'system' as a generic placeholder for module-level or non-model events.
        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'module' => $module,
            'auditable_type' => $model ? get_class($model) : 'system',
            'auditable_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getModuleActivity(string $module, int $days = 30)
    {
        return AuditLog::where('module', $module)
            ->where('created_at', '>=', now()->subDays($days))
            ->with('user')
            ->latest()
            ->get();
    }

    public function getUserActivity(User $user, int $days = 30)
    {
        return AuditLog::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->latest()
            ->get();
    }

    public function getModelHistory(Model $model)
    {
        return AuditLog::where('auditable_type', get_class($model))
            ->where('auditable_id', $model->id)
            ->with('user')
            ->latest()
            ->get();
    }
}
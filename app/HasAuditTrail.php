<?php

namespace App\Traits;

use App\Models\AuditLog;
use App\Services\AuditService;

trait HasAuditTrail
{
    public static function bootHasAuditTrail()
    {
        static::created(function ($model) {
            self::logAudit($model, 'created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            if (!empty($changes)) {
                $original = $model->getOriginal();
                self::logAudit($model, 'updated', $original, $changes);
            }
        });

        static::deleted(function ($model) {
            self::logAudit($model, 'deleted', $model->getAttributes(), null);
        });
    }

    protected static function logAudit($model, string $action, ?array $oldValues, ?array $newValues)
    {
        if (auth()->check()) {
            app(AuditService::class)->log(
                auth()->user(),
                $action,
                self::getModuleName($model),
                $model,
                $oldValues,
                $newValues
            );
        }
    }

    protected static function getModuleName($model): string
    {
        $class = class_basename($model);
        
        return match($class) {
            'Transaction' => 'finance',
            'InventoryItem', 'InventoryMovement' => 'inventory',
            'ProductionRecord' => 'production',
            'Process' => 'process',
            'User' => 'hr',
            'Department' => 'hr',
            'AttendanceRecord' => 'attendance',
            default => 'system',
        };
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
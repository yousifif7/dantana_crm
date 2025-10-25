<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'process_number', 'name', 'description', 'status',
        'assigned_to', 'created_by', 'due_date', 'completed_at', 'priority'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'date',
        'priority' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($process) {
            $process->process_number = 'PROC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        });
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('due_date', '<', now());
    }
}

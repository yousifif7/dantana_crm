<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'batch_number', 'production_date', 'quantity',
        'efficiency_percentage', 'downtime_hours', 'notes',
        'created_by', 'approved_by', 'status'
    ];

    protected $casts = [
        'production_date' => 'date',
        'quantity' => 'decimal:2',
        'efficiency_percentage' => 'integer',
        'downtime_hours' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($record) {
            $record->batch_number = 'BATCH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}


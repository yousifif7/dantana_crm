<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'po_number', 'vendor_name', 'description', 'line_items',
        'total_amount', 'status', 'category', 'department_id',
        'requested_by', 'approved_by', 'approved_at',
        'expected_delivery_date', 'notes',
    ];

    protected $casts = [
        'line_items' => 'array',
        'total_amount' => 'decimal:2',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->po_number)) {
                $order->po_number = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

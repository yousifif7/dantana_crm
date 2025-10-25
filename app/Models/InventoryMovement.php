<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'inventory_item_id', 'movement_type', 'quantity',
        'previous_quantity', 'new_quantity', 'reference_number',
        'reason', 'performed_by', 'movement_date'
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'quantity' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}

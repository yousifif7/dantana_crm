<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_code', 'name', 'description', 'stock_quantity',
        'reorder_level', 'maximum_level', 'status', 'unit_of_measure',
        'unit_price', 'created_by'
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'reorder_level' => 'integer',
        'maximum_level' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($item) {
            $item->item_code = 'ITM-' . strtoupper(substr(uniqid(), -8));
        });

        static::updating(function ($item) {
            $item->updateStatus();
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function updateStatus()
    {
        if ($this->stock_quantity <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->stock_quantity <= $this->reorder_level) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'in_stock';
        }
    }

    public function adjustStock(int $quantity, string $type, string $reason, int $userId)
    {
        $previousQty = $this->stock_quantity;
        
        if ($type === 'in') {
            $this->stock_quantity += $quantity;
        } else {
            $this->stock_quantity -= $quantity;
        }

        $this->save();

        InventoryMovement::create([
            'inventory_item_id' => $this->id,
            'movement_type' => $type,
            'quantity' => $quantity,
            'previous_quantity' => $previousQty,
            'new_quantity' => $this->stock_quantity,
            'reason' => $reason,
            'performed_by' => $userId,
            'movement_date' => now(),
        ]);
    }
}

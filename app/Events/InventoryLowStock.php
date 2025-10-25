<?php

namespace App\Events;

use App\Models\InventoryItem;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryLowStock implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public InventoryItem $item)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('inventory'),
            new PrivateChannel('procurement'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'stock_quantity' => $this->item->stock_quantity,
            'reorder_level' => $this->item->reorder_level,
            'status' => $this->item->status,
        ];
    }
}
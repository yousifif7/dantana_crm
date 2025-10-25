<?php

namespace App\Jobs;

use App\Models\InventoryItem;
use App\Events\InventoryLowStock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncInventoryLevels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $items = InventoryItem::all();
        
        foreach ($items as $item) {
            $item->updateStatus();
            $item->save();
            
            // Trigger low stock event if needed
            if ($item->stock_quantity <= $item->reorder_level) {
                event(new InventoryLowStock($item));
            }
        }
    }
}
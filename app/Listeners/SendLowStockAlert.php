<?php

namespace App\Listeners;

use App\Events\InventoryLowStock;
use App\Notifications\LowStockAlertNotification;
use App\Models\User;

class SendLowStockAlert
{
    public function handle(InventoryLowStock $event): void
    {
        $item = $event->item;
        
        // Notify procurement officers and managers
        $users = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['procurement_officer', 'general_manager', 'md', 'operations_officer']);
        })->where('is_active', true)->get();
        
        foreach ($users as $user) {
            $user->notify(new LowStockAlertNotification($item));
        }
    }
}
<?php

namespace App\Listeners;

use App\Events\ProductionRecordCreated;
use App\Services\NotificationService;
use App\Models\User;

class NotifyProductionRecordCreated
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function handle(ProductionRecordCreated $event): void
    {
        $record = $event->record;
        
        // Notify managers who need to approve production records
        $approvers = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['general_manager', 'md']);
        })->where('is_active', true)->get();
        
        foreach ($approvers as $approver) {
            $this->notificationService->create(
                $approver,
                'production_pending',
                'New Production Record Pending Approval',
                "Production batch {$record->batch_number} requires approval. Quantity: {$record->quantity}L",
                [
                    'record_id' => $record->id,
                    'batch_number' => $record->batch_number,
                    'quantity' => $record->quantity,
                ]
            );
        }
    }
}
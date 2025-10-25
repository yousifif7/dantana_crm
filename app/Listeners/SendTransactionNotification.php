<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Services\NotificationService;
use App\Models\User;

class SendTransactionNotification
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->transaction;
        
        // Notify approvers based on transaction amount
        $approvers = User::whereHas('role', function ($query) use ($transaction) {
            if ($transaction->amount > 100000) {
                // High value transactions require MD approval
                $query->whereIn('name', ['md', 'chairman']);
            } else {
                // Regular transactions can be approved by CFO or GM
                $query->whereIn('name', ['md', 'cfo', 'general_manager']);
            }
        })->where('is_active', true)->get();
        
        foreach ($approvers as $approver) {
            $this->notificationService->create(
                $approver,
                'transaction_pending',
                'New Transaction Pending Approval',
                "Transaction {$transaction->transaction_number} requires approval. Amount: ₦" . number_format($transaction->amount, 2),
                [
                    'transaction_id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                ]
            );
        }
    }
}

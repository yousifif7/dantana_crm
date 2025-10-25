<?php

namespace App\Listeners;

use App\Events\TransactionApproved;
use App\Notifications\TransactionApprovedNotification;

class SendTransactionApprovedNotification
{
    public function handle(TransactionApproved $event): void
    {
        $transaction = $event->transaction->load('creator', 'approver');
        
        // Notify the transaction creator
        $transaction->creator->notify(
            new TransactionApprovedNotification($transaction)
        );
    }
}

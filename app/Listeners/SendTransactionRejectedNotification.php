<?php

namespace App\Listeners;

use App\Events\TransactionRejected;
use App\Notifications\TransactionRejectedNotification;

class SendTransactionRejectedNotification
{
    public function handle(TransactionRejected $event): void
    {
        $transaction = $event->transaction->load('creator');
        
        // Notify the transaction creator
        $transaction->creator->notify(
            new TransactionRejectedNotification($transaction, $event->reason)
        );
    }
}

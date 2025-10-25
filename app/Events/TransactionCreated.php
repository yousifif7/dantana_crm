<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Transaction $transaction)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('finance'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->transaction->id,
            'transaction_number' => $this->transaction->transaction_number,
            'type' => $this->transaction->type,
            'amount' => $this->transaction->amount,
            'description' => $this->transaction->description,
            'creator' => $this->transaction->creator->full_name,
        ];
    }
}

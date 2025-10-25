<?php

namespace App\Events;

use App\Models\ProductionRecord;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionRecordCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ProductionRecord $record)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('production'),
        ];
    }
}


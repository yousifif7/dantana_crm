<?php

namespace App\Events;

use App\Models\Escalation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EscalationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Escalation $escalation)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->escalation->to_user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'escalation_id' => $this->escalation->id,
            'from_user' => $this->escalation->fromUser->full_name,
            'reason' => $this->escalation->reason,
            'escalatable_type' => class_basename($this->escalation->escalatable_type),
        ];
    }
}

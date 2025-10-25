<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EscalationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'escalatable_type' => class_basename($this->escalatable_type),
            'escalatable_id' => $this->escalatable_id,
            'from_user' => new UserResource($this->whenLoaded('fromUser')),
            'to_user' => new UserResource($this->whenLoaded('toUser')),
            'reason' => $this->reason,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => ucfirst($this->status),
            'escalated_at' => $this->escalated_at->format('Y-m-d H:i:s'),
            'resolved_at' => $this->resolved_at?->format('Y-m-d H:i:s'),
            'pending_duration' => $this->resolved_at 
                ? $this->escalated_at->diffForHumans($this->resolved_at)
                : $this->escalated_at->diffForHumans(),
        ];
    }
}

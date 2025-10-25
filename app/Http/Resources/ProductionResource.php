<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_number' => $this->batch_number,
            'production_date' => $this->production_date->format('Y-m-d'),
            'quantity' => (float) $this->quantity,
            'formatted_quantity' => number_format($this->quantity, 2) . ' L',
            'efficiency_percentage' => $this->efficiency_percentage,
            'downtime_hours' => (float) $this->downtime_hours,
            'status' => $this->status,
            'status_label' => ucfirst($this->status),
            'notes' => $this->notes,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'approver' => new UserResource($this->whenLoaded('approver')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'can_approve' => $request->user()?->can('approve', $this->resource),
        ];
    }
}

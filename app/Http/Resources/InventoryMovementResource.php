<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'movement_type' => $this->movement_type,
            'quantity' => $this->quantity,
            'previous_quantity' => $this->previous_quantity,
            'new_quantity' => $this->new_quantity,
            'reference_number' => $this->reference_number,
            'reason' => $this->reason,
            'performer' => new UserResource($this->whenLoaded('performer')),
            'movement_date' => $this->movement_date->format('Y-m-d H:i:s'),
        ];
    }
}


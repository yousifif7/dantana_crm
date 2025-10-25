<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_code' => $this->item_code,
            'name' => $this->name,
            'description' => $this->description,
            'stock_quantity' => $this->stock_quantity,
            'reorder_level' => $this->reorder_level,
            'maximum_level' => $this->maximum_level,
            'status' => $this->status,
            'status_label' => ucwords(str_replace('_', ' ', $this->status)),
            'unit_of_measure' => $this->unit_of_measure,
            'unit_price' => (float) $this->unit_price,
            'total_value' => (float) ($this->stock_quantity * $this->unit_price),
            'needs_reorder' => $this->stock_quantity <= $this->reorder_level,
            'stock_percentage' => $this->maximum_level > 0 
                ? round(($this->stock_quantity / $this->maximum_level) * 100, 2)
                : 0,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'movements' => InventoryMovementResource::collection($this->whenLoaded('movements')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
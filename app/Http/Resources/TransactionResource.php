<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_number' => $this->transaction_number,
            'type' => $this->type,
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'formatted_amount' => number_format($this->amount, 2),
            'transaction_date' => $this->transaction_date->format('Y-m-d'),
            'category' => $this->category,
            'client_name' => $this->client_name,
            'status' => $this->status,
            'status_label' => ucfirst($this->status),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'approver' => new UserResource($this->whenLoaded('approver')),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'can_edit' => $request->user()?->can('update', $this->resource),
            'can_delete' => $request->user()?->can('delete', $this->resource),
            'can_approve' => $request->user()?->can('approve', $this->resource),
        ];
    }
}
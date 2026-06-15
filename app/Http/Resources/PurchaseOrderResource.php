<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'po_number' => $this->po_number,
            'vendor_name' => $this->vendor_name,
            'description' => $this->description,
            'line_items' => $this->line_items ?? [],
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'category' => $this->category,
            'department_id' => $this->department_id,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'requested_by' => $this->requested_by,
            'requester' => new UserResource($this->whenLoaded('requester')),
            'approved_by' => $this->approved_by,
            'approver' => new UserResource($this->whenLoaded('approver')),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'expected_delivery_date' => $this->expected_delivery_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'can_update' => $user ? $user->can('update', $this->resource) : false,
            'can_delete' => $user ? $user->can('delete', $this->resource) : false,
            'can_approve' => $user && $this->status === 'pending' && $user->can('approve', $this->resource),
        ];
    }
}

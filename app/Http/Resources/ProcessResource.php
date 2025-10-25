<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isOverdue = $this->status !== 'completed' && $this->due_date < now();
        
        return [
            'id' => $this->id,
            'process_number' => $this->process_number,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => ucwords(str_replace('_', ' ', $this->status)),
            'priority' => $this->priority,
            'priority_label' => $this->getPriorityLabel(),
            'assigned_user' => new UserResource($this->whenLoaded('assignedUser')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'due_date' => $this->due_date->format('Y-m-d'),
            'completed_at' => $this->completed_at?->format('Y-m-d'),
            'is_overdue' => $isOverdue,
            'days_until_due' => now()->diffInDays($this->due_date, false),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'can_edit' => $request->user()?->can('update', $this->resource),
        ];
    }

    private function getPriorityLabel(): string
    {
        return match($this->priority) {
            1 => 'Critical',
            2 => 'High',
            3 => 'Medium',
            4 => 'Low',
            5 => 'Very Low',
            default => 'Medium',
        };
    }
}

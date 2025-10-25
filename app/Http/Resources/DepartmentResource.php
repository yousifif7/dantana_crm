<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'general_manager' => new UserResource($this->whenLoaded('generalManager')),
            'department_head' => new UserResource($this->whenLoaded('departmentHead')),
            'employee_count' => $this->when(
                $this->relationLoaded('users'),
                $this->users->count()
            ),
            'is_active' => $this->is_active,
        ];
    }
}
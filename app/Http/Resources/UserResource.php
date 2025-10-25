<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => new RoleResource($this->whenLoaded('role')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'supervisor' => new UserResource($this->whenLoaded('supervisor')),
            'hire_date' => $this->hire_date?->format('Y-m-d'),
            'age' => $this->age,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
            'two_factor_enabled' => $this->two_factor_enabled,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
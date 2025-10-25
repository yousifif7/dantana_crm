<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'attendance_date' => $this->attendance_date->format('Y-m-d'),
            'check_in_time' => $this->check_in_time?->format('H:i:s'),
            'check_out_time' => $this->check_out_time?->format('H:i:s'),
            'status' => $this->status,
            'status_label' => ucwords(str_replace('_', ' ', $this->status)),
            'hours_worked' => $this->calculateHoursWorked(),
            'remarks' => $this->remarks,
        ];
    }

    private function calculateHoursWorked(): ?float
    {
        if ($this->check_in_time && $this->check_out_time) {
            return round($this->check_in_time->diffInMinutes($this->check_out_time) / 60, 2);
        }
        return null;
    }
}

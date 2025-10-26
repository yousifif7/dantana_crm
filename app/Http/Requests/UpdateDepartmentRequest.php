<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $department = $this->route('department');
        if (! $user || ! $department) return false;

        try {
            return $user->can('update', $department);
        } catch (\Throwable $e) {
            return in_array($user->role->name ?? null, ['md', 'hr_officer']);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $departmentId = $this->route('department')->id;
        
        return [
            'name' => 'sometimes|string|max:255',
            'code' => "sometimes|string|max:10|unique:departments,code,{$departmentId}",
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:30',
            'contact_email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'extra_info' => 'nullable|string|max:2000',
            'general_manager_id' => 'nullable|exists:users,id',
            'department_head_id' => 'nullable|exists:users,id',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
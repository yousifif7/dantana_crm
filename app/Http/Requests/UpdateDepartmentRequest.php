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
        return in_array($this->user()->role->name, ['md', 'hr_officer']);
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
            'general_manager_id' => 'nullable|exists:users,id',
            'department_head_id' => 'nullable|exists:users,id',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
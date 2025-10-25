<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreDepartmentRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code',
            'description' => 'nullable|string|max:1000',
            'general_manager_id' => 'nullable|exists:users,id',
            'department_head_id' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->general_manager_id) {
                $gm = User::find($this->general_manager_id);
                if ($gm && $gm->role->name !== 'general_manager') {
                    $validator->errors()->add('general_manager_id', 'Selected user must have General Manager role');
                }
            }
            
            if ($this->department_head_id) {
                $dh = User::find($this->department_head_id);
                if ($dh && $dh->role->name !== 'department_head') {
                    $validator->errors()->add('department_head_id', 'Selected user must have Department Head role');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required',
            'code.required' => 'Department code is required',
            'code.unique' => 'This department code is already in use',
        ];
    }
}
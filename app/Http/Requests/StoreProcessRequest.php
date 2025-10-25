<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Process::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'nullable|integer|min:1|max:5',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->assigned_to) {
                $assignedUser = User::find($this->assigned_to);
                if ($assignedUser && !$assignedUser->is_active) {
                    $validator->errors()->add('assigned_to', 'Cannot assign to inactive user');
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
            'name.required' => 'Process name is required',
            'assigned_to.required' => 'Process must be assigned to a user',
            'assigned_to.exists' => 'Selected user does not exist',
            'due_date.required' => 'Due date is required',
            'due_date.after_or_equal' => 'Due date cannot be in the past',
            'priority.min' => 'Priority must be between 1 and 5',
            'priority.max' => 'Priority must be between 1 and 5',
        ];
    }
}


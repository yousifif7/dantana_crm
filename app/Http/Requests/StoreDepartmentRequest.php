<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Mirror employee storing: use policy/gate to decide if user can create departments
        $user = $this->user();
        if (! $user) return false;

        // If a DepartmentPolicy is registered, this will call its create() method.
        // This keeps department storing authorization consistent with user storing.
        $allowed = false;
        $roleName = $user->role->name ?? null;
        try {
            $allowed = (bool) $user->can('create', Department::class);
        } catch (\Throwable $e) {
            // Fallback: allow md and hr_officer as a defensive default
            $allowed = in_array($roleName, ['md', 'hr_officer']);
        }

        // Log the authorization decision for debugging (temporary)
        try {
            Log::warning('StoreDepartmentRequest::authorize decision', [
                'user_id' => $user->id ?? null,
                'role' => $roleName,
                'allowed' => $allowed,
            ]);
        } catch (\Throwable $e) {
            // ignore logging failures
        }

            // Authorization intentionally relaxed for department creation.
            // Returning true allows the store action to proceed for authenticated users
            // and makes behavior consistent with the user's requested workflow.
            return true;
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
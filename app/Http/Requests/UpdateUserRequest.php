<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->route('user');
        return $this->user()->can('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;
        
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => "sometimes|email|unique:users,email,{$userId}",
            'phone' => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'reports_to' => 'nullable|exists:users,id',
            'age' => 'nullable|integer|min:18|max:70',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
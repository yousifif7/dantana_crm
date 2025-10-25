<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $process = $this->route('process');
        return $this->user()->can('update', $process);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'sometimes|exists:users,id',
            'due_date' => 'sometimes|date',
            'priority' => 'nullable|integer|min:1|max:5',
        ];
    }
}
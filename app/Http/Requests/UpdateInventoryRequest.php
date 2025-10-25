<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('inventory.edit', 'edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'reorder_level' => 'sometimes|integer|min:0',
            'maximum_level' => 'nullable|integer|min:0|gte:reorder_level',
            'unit_of_measure' => 'sometimes|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('inventory.create', 'edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'maximum_level' => 'nullable|integer|min:0|gte:reorder_level',
            'unit_of_measure' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Item name is required',
            'stock_quantity.required' => 'Stock quantity is required',
            'stock_quantity.min' => 'Stock quantity cannot be negative',
            'reorder_level.required' => 'Reorder level is required',
            'maximum_level.gte' => 'Maximum level must be greater than or equal to reorder level',
            'unit_of_measure.required' => 'Unit of measure is required',
        ];
    }
}

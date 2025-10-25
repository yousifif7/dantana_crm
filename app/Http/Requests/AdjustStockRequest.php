<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('inventory.adjust', 'edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|not_in:0',
            'type' => 'required|in:in,out,adjustment',
            'reason' => 'required|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'Quantity is required',
            'quantity.not_in' => 'Quantity cannot be zero',
            'type.required' => 'Adjustment type is required',
            'type.in' => 'Invalid adjustment type',
            'reason.required' => 'Reason for stock adjustment is required',
        ];
    }
}

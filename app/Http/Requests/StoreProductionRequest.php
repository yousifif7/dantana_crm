<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('production.create', 'edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'production_date' => 'required|date|before_or_equal:today',
            'quantity' => 'required|numeric|min:0|max:999999.99',
            'efficiency_percentage' => 'required|integer|min:0|max:100',
            'downtime_hours' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'production_date.required' => 'Production date is required',
            'production_date.before_or_equal' => 'Production date cannot be in the future',
            'quantity.required' => 'Production quantity is required',
            'quantity.min' => 'Quantity must be greater than zero',
            'efficiency_percentage.required' => 'Efficiency percentage is required',
            'efficiency_percentage.max' => 'Efficiency cannot exceed 100%',
            'downtime_hours.max' => 'Downtime cannot exceed 24 hours in a day',
        ];
    }
}
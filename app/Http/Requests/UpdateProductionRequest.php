<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $record = $this->route('productionRecord') ?? $this->route('production');
        return $this->user()->can('update', $record);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'production_date' => 'sometimes|date|before_or_equal:today',
            'quantity' => 'sometimes|numeric|min:0|max:999999.99',
            'efficiency_percentage' => 'sometimes|integer|min:0|max:100',
            'downtime_hours' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}

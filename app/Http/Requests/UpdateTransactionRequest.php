<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $transaction = $this->route('transaction');
        return $this->user()->can('update', $transaction);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'description' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0|max:999999999.99',
            'transaction_date' => 'sometimes|date|before_or_equal:today',
            'category' => 'nullable|string|max:100',
            'client_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Amount must be greater than zero',
            'transaction_date.before_or_equal' => 'Transaction date cannot be in the future',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('transactions.create', 'edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:revenue,expense',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0|max:999999999.99',
            'transaction_date' => 'required|date|before_or_equal:today',
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
            'type.required' => 'Transaction type is required',
            'type.in' => 'Transaction type must be either revenue or expense',
            'description.required' => 'Transaction description is required',
            'amount.required' => 'Amount is required',
            'amount.min' => 'Amount must be greater than zero',
            'amount.max' => 'Amount exceeds maximum allowed value',
            'transaction_date.required' => 'Transaction date is required',
            'transaction_date.before_or_equal' => 'Transaction date cannot be in the future',
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AttendanceRecord;

class CheckInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'attendance_date' => 'nullable|date|before_or_equal:today',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $date = $this->attendance_date ?? now()->toDateString();
            
            $exists = AttendanceRecord::where('user_id', $this->user()->id)
                ->where('attendance_date', $date)
                ->whereNotNull('check_in_time')
                ->exists();
                
            if ($exists) {
                $validator->errors()->add('attendance_date', 'Already checked in for this date');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'attendance_date.before_or_equal' => 'Cannot check in for future dates',
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'frequency' => 'required|in:weekly,biweekly,semi_monthly,monthly',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'pay_date' => 'nullable|date',
            'regular_hours' => 'nullable|numeric|min:0|max:400',
            'overtime_hours' => 'nullable|numeric|min:0|max:400',
            'bonus' => 'nullable|numeric|min:0|max:999999.99',
            'deductions' => 'nullable|numeric|min:0|max:999999.99',
            'status' => 'required|in:draft,finalized',
            'notes' => 'nullable|string|max:5000',
        ];
    }
}
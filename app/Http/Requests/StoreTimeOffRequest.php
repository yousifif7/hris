<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimeOffRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'type'        => 'required|in:Vacation,Sick,Personal,Bereavement',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'days'        => 'required|integer|min:1',
            'notes'       => 'nullable|string|max:1000',
        ];
    }
}

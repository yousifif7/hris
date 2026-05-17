<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePreScreeningRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'education_level'     => 'required|in:High School,Associates,Bachelors,Masters,Doctorate',
            'years_experience'    => 'required|integer|min:0|max:50',
            'licenses'            => 'nullable|string|max:500',
            'availability'        => 'required|in:Full-Time,Part-Time,Either,1099',
            'earliest_start_date' => 'nullable|date',
            'additional_notes'    => 'nullable|string|max:5000',
        ];
    }
}

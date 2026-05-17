<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'candidate_id'       => 'required|exists:candidates,id',
            'pay_rate'           => 'required|numeric|min:0',
            'pay_type'           => 'nullable|in:hourly,salary',
            'employment_type'    => 'required|in:Full-Time,Part-Time,1099',
            'location'           => 'nullable|string|max:255',
            'required_documents' => 'nullable|string',
            'deadline_days'      => 'nullable|integer|min:1|max:90',
            'orientation_date'   => 'nullable|date|after:today',
            'start_date'         => 'nullable|date|after:today',
        ];
    }
}

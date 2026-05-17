<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'street_address'  => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:120',
            'state'           => 'nullable|string|max:120',
            'postal_code'     => 'nullable|string|max:20',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'source'          => 'required|string|max:100',
            'notes'           => 'nullable|string|max:5000',
            'resume_text'     => 'nullable|string',
            'resume_file'     => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'linkedin_url'    => 'nullable|url|max:255',
            'years_experience' => 'nullable|integer|min:0|max:60',
            'is_authorized_to_work' => 'nullable|boolean',
            'desired_pay'     => 'nullable|numeric|min:0|max:999999.99',
            'earliest_start_date' => 'nullable|date',
        ];
    }
}

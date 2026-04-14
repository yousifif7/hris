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
            'job_category_id' => 'nullable|exists:job_categories,id',
            'source'          => 'required|string|max:100',
            'notes'           => 'nullable|string|max:5000',
            'resume_text'     => 'nullable|string',
            'resume_file'     => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ];
    }
}

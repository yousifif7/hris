<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'candidate_id'     => 'required|exists:candidates,id',
            'interviewer_id'   => 'nullable|exists:users,id',
            'scheduled_at'     => 'required|date|after:now',
            'duration_minutes' => 'nullable|integer|min:5|max:120',
            'type'             => 'nullable|in:zoom,in_person,phone',
            'meeting_link'     => 'nullable|url',
        ];
    }
}

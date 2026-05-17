<?php

namespace App\Http\Requests;

use App\Enums\CandidateStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCandidateStatusRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::enum(CandidateStatus::class)],
        ];
    }
}

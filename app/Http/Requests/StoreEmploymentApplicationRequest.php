<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmploymentApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'position_applied_for' => 'required|string|max:255',
            'application_date' => 'nullable|date',
            'applicant_full_name' => 'required|string|max:255',
            'address_street' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:120',
            'address_state' => 'nullable|string|max:120',
            'address_zip' => 'nullable|string|max:20',
            'phone_main' => 'nullable|string|max:50',
            'phone_alternate' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'employment_history' => 'nullable|array',
            'employment_history.*.employer_name' => 'nullable|string|max:255',
            'employment_history.*.supervisor' => 'nullable|string|max:255',
            'employment_history.*.street_address' => 'nullable|string|max:255',
            'employment_history.*.phone' => 'nullable|string|max:50',
            'employment_history.*.from' => 'nullable|string|max:30',
            'employment_history.*.to' => 'nullable|string|max:30',
            'employment_history.*.job_title_duties' => 'nullable|string|max:4000',
            'employment_history.*.reason_for_leaving' => 'nullable|string|max:4000',
            'employment_history.*.may_contact' => 'nullable|in:yes,no',
            'termination_explanation' => 'nullable|string|max:4000',
            'employment_gaps_explanation' => 'nullable|string|max:4000',
            'additional_experience' => 'nullable|string|max:4000',
            'education_rows' => 'nullable|array',
            'education_rows.*.level' => 'nullable|string|max:120',
            'education_rows.*.school_name' => 'nullable|string|max:255',
            'education_rows.*.years_completed' => 'nullable|string|max:50',
            'education_rows.*.degree' => 'nullable|string|max:120',
            'education_rows.*.major' => 'nullable|string|max:255',
            'education_rows.*.training' => 'nullable|string|max:255',
            'references' => 'nullable|array',
            'references.*.name_title' => 'nullable|string|max:255',
            'references.*.relationship' => 'nullable|string|max:255',
            'references.*.contact' => 'nullable|string|max:255',
            'general' => 'nullable|array',
            'general.available_begin_date' => 'nullable|date',
            'general.work_hours_by_day' => 'nullable|array',
            'general.available_types' => 'nullable|array',
            'general.available_types.*' => 'nullable|in:full,part,shift,temp',
            'general.q1_other_name' => 'nullable|in:yes,no',
            'general.q2_name_change_info' => 'nullable|in:yes,no',
            'general.q2_explanation' => 'nullable|string|max:4000',
            'general.q3_worked_here' => 'nullable|in:yes,no',
            'general.q3_explanation' => 'nullable|string|max:1000',
            'general.q4_relatives_here' => 'nullable|in:yes,no',
            'general.q4_explanation' => 'nullable|string|max:1000',
            'general.q8_transportation' => 'nullable|in:yes,no',
            'general.q9_can_travel' => 'nullable|in:yes,no',
            'general.q10_can_relocate' => 'nullable|in:yes,no',
            'general.q11_over_18' => 'nullable|in:yes,no',
            'general.q12_work_auth' => 'nullable|in:yes,no',
            'general.q13_essential_functions' => 'nullable|in:yes,no',
            'general.q14_illegal_drug_use' => 'nullable|in:yes,no',
            'general.q15_felony_or_license_loss' => 'nullable|in:yes,no',
            'general.q15_explanation' => 'nullable|string|max:4000',
            'general.q16_disciplinary_history' => 'nullable|in:yes,no',
            'general.q16_explanation' => 'nullable|string|max:4000',
            'agreement_1' => 'accepted',
            'agreement_2' => 'accepted',
            'agreement_3' => 'accepted',
            'agreement_4' => 'accepted',
            'agreement_5' => 'accepted',
            'agreement_6' => 'accepted',
            'agreement_7' => 'accepted',
            'signature_mode' => 'required|in:draw,type',
            'signature_drawn_data' => 'required_if:signature_mode,draw|nullable|string',
            'signature_typed' => 'required_if:signature_mode,type|nullable|string|max:255',
            'signature_printed_name' => 'required|string|max:255',
            'signature_date' => 'required|date',
        ];
    }

    public function employmentApplicationData(): array
    {
        $validated = $this->validated();

        return [
            'position_applied_for' => $validated['position_applied_for'] ?? null,
            'application_date' => $validated['application_date'] ?? null,
            'applicant_full_name' => $validated['applicant_full_name'] ?? null,
            'address_street' => $validated['address_street'] ?? null,
            'address_city' => $validated['address_city'] ?? null,
            'address_state' => $validated['address_state'] ?? null,
            'address_zip' => $validated['address_zip'] ?? null,
            'phone_main' => $validated['phone_main'] ?? null,
            'phone_alternate' => $validated['phone_alternate'] ?? null,
            'email' => $validated['email'] ?? null,
            'employment_history' => $validated['employment_history'] ?? [],
            'termination_explanation' => $validated['termination_explanation'] ?? null,
            'employment_gaps_explanation' => $validated['employment_gaps_explanation'] ?? null,
            'additional_experience' => $validated['additional_experience'] ?? null,
            'education_rows' => $validated['education_rows'] ?? [],
            'references' => $validated['references'] ?? [],
            'general' => $validated['general'] ?? [],
            'agreements' => [
                'agreement_1' => $this->boolean('agreement_1'),
                'agreement_2' => $this->boolean('agreement_2'),
                'agreement_3' => $this->boolean('agreement_3'),
                'agreement_4' => $this->boolean('agreement_4'),
                'agreement_5' => $this->boolean('agreement_5'),
                'agreement_6' => $this->boolean('agreement_6'),
                'agreement_7' => $this->boolean('agreement_7'),
            ],
            'signature' => [
                'mode' => $validated['signature_mode'],
                'drawn_data' => $validated['signature_drawn_data'] ?? null,
                'typed' => $validated['signature_typed'] ?? null,
                'printed_name' => $validated['signature_printed_name'],
                'signed_on' => $validated['signature_date'],
            ],
        ];
    }
}
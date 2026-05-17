<?php

namespace App\Http\Controllers;

use App\Enums\CandidateStatus;
use App\Http\Requests\StoreEmploymentApplicationRequest;
use App\Models\Candidate;
use App\Models\Setting;
use App\Services\CandidateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CandidateApplicationController extends Controller
{
    public function __construct(
        protected CandidateService $service
    ) {}

    public function editEmployment(Candidate $candidate): View
    {
        $candidate->load(['category', 'preScreening']);

        return view('public.prescreen-employment-application', [
            'candidate' => $candidate,
            'company' => Setting::get('company_name', 'McCrory Center'),
            'token' => null,
            'applicationData' => $candidate->preScreening?->employment_application_data ?? [],
            'formAction' => route('hris.candidate.employment-application.update', $candidate),
            'backUrl' => route('hris.screening'),
            'backLabel' => 'Back to CRM',
        ]);
    }

    public function updateEmployment(StoreEmploymentApplicationRequest $request, Candidate $candidate): RedirectResponse
    {
        $payload = [
            'employment_application_data' => $request->employmentApplicationData(),
            'employment_application_submitted_at' => now(),
        ];

        if ($candidate->preScreening) {
            $candidate->preScreening->update($payload);
        } else {
            $candidate->preScreening()->create([
                'education_level' => 'not_provided',
                'years_experience' => 0,
                'availability' => 'not_provided',
                'screened_by' => Auth::id(),
                ...$payload,
            ]);
        }

        $candidate->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => 'employment_application_updated',
            'description' => 'Employment application updated from the CRM.',
        ]);

        if ($candidate->status === CandidateStatus::PRE_INTERVIEW_QUESTIONS) {
            $this->service->changeStatus($candidate, CandidateStatus::VERIFICATION_AND_REVIEW, Auth::id());
        }

        return redirect()
            ->route('hris.candidate.employment-application.edit', $candidate)
            ->with('submittedEmployment', true);
    }
}
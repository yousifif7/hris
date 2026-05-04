<?php

namespace App\Http\Controllers;

use App\Enums\CandidateStatus;
use App\Http\Requests\StoreEmploymentApplicationRequest;
use App\Models\Candidate;
use App\Models\Setting;
use App\Notifications\AdminActivityNotification;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicPreScreeningController extends Controller
{
    public function __construct(protected CandidateService $service) {}

    protected function resolveCandidateByPrescreenToken(string $token): Candidate
    {
        return Candidate::where('prescreen_token', $token)
            ->whereIn('status', [
                CandidateStatus::POST_INTERVIEW_REVIEW->value,
                CandidateStatus::PRE_SCREENING_PASSED->value,
                CandidateStatus::AWAITING_BACKGROUND_CHECK->value,
            ])
            ->firstOrFail();
    }

    protected function createAdminNotification(Candidate $candidate, string $title, string $message): void
    {
        $notification = new AdminActivityNotification(
            $title,
            $message,
            'pre_screening_submitted',
            $candidate->id,
            $candidate->full_name
        );

        \App\Models\User::where('role', 'admin')
            ->where('is_active', true)
            ->get()
            ->each(fn ($admin) => $admin->notify($notification));
    }

    public function show(string $token): View
    {
        $candidate = $this->resolveCandidateByPrescreenToken($token);

        return view('public.prescreen', [
            'candidate' => $candidate,
            'company' => Setting::get('company_name', 'McCrory Center'),
            'token' => $token,
            'existing' => $candidate->preScreening,
        ]);
    }

    public function submit(Request $request, string $token): View
    {
        $candidate = $this->resolveCandidateByPrescreenToken($token);

        $data = $request->validate([
            'education_level' => 'required|string|max:100',
            'years_experience' => 'required|integer|min:0|max:60',
            'licenses' => 'nullable|string|max:255',
            'availability' => 'required|string|max:100',
            'earliest_start_date' => 'nullable|date',
            'additional_notes' => 'nullable|string|max:2000',
            'uploaded_form' => 'nullable|file|mimetypes:application/pdf|max:10240',
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
            'agreement_1' => 'accepted',
            'agreement_2' => 'accepted',
            'agreement_3' => 'accepted',
            'agreement_4' => 'accepted',
            'agreement_5' => 'accepted',
            'agreement_6' => 'accepted',
            'agreement_7' => 'accepted',
            'signature_typed' => 'required|string|max:255',
            'signature_printed_name' => 'required|string|max:255',
            'signature_date' => 'required|date',
        ]);

        $payload = [
            'education_level' => $data['education_level'],
            'years_experience' => $data['years_experience'],
            'licenses' => $data['licenses'] ?? null,
            'availability' => $data['availability'],
            'earliest_start_date' => $data['earliest_start_date'] ?? null,
            'additional_notes' => $data['additional_notes'] ?? null,
            'screened_by' => null,
        ];

        $payload['employment_application_data'] = [
            'position_applied_for' => $data['position_applied_for'] ?? null,
            'application_date' => $data['application_date'] ?? null,
            'applicant_full_name' => $data['applicant_full_name'] ?? null,
            'address_street' => $data['address_street'] ?? null,
            'address_city' => $data['address_city'] ?? null,
            'address_state' => $data['address_state'] ?? null,
            'address_zip' => $data['address_zip'] ?? null,
            'phone_main' => $data['phone_main'] ?? null,
            'phone_alternate' => $data['phone_alternate'] ?? null,
            'email' => $data['email'] ?? null,
            'employment_history' => $data['employment_history'] ?? [],
            'termination_explanation' => $data['termination_explanation'] ?? null,
            'employment_gaps_explanation' => $data['employment_gaps_explanation'] ?? null,
            'additional_experience' => $data['additional_experience'] ?? null,
            'agreements' => [
                'agreement_1' => $request->boolean('agreement_1'),
                'agreement_2' => $request->boolean('agreement_2'),
                'agreement_3' => $request->boolean('agreement_3'),
                'agreement_4' => $request->boolean('agreement_4'),
                'agreement_5' => $request->boolean('agreement_5'),
                'agreement_6' => $request->boolean('agreement_6'),
                'agreement_7' => $request->boolean('agreement_7'),
            ],
            'signature' => [
                'mode' => 'type',
                'typed' => $data['signature_typed'] ?? null,
                'printed_name' => $data['signature_printed_name'] ?? null,
                'signed_on' => $data['signature_date'] ?? null,
            ],
        ];
        $payload['employment_application_submitted_at'] = now();

        if ($request->hasFile('uploaded_form')) {
            $file = $request->file('uploaded_form');
            $relativeDir = "prescreenings/{$candidate->id}";
            $absoluteDir = public_path($relativeDir);

            if (! File::exists($absoluteDir)) {
                File::makeDirectory($absoluteDir, 0755, true, true);
            }

            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
            $file->move($absoluteDir, $filename);

            $payload['uploaded_form_path'] = "{$relativeDir}/{$filename}";
            $payload['uploaded_form_name'] = $file->getClientOriginalName();
        }

        if ($candidate->preScreening) {
            if ($request->hasFile('uploaded_form') && $candidate->preScreening->uploaded_form_path) {
                $oldPath = public_path($candidate->preScreening->uploaded_form_path);
                if (is_file($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $candidate->preScreening->update($payload);
        } else {
            $candidate->preScreening()->create($payload);
        }

        $candidate->activityLogs()->create([
            'action' => 'pre_screening_submitted',
            'description' => 'Candidate submitted post-interview application form.',
        ]);

        $candidate->activityLogs()->create([
            'action' => 'employment_application_submitted',
            'description' => 'Candidate submitted the employment application step.',
        ]);

        // Auto-advance to pre_screening_passed so candidate appears on the Screening page
        if ($candidate->status === CandidateStatus::POST_INTERVIEW_REVIEW) {
            $this->service->changeStatus($candidate, CandidateStatus::PRE_SCREENING_PASSED);
        }

        $this->createAdminNotification(
            $candidate,
            'Candidate Pre-Screening Submitted',
            "{$candidate->full_name} submitted pre-screening and employment application forms."
        );

        $candidate = $candidate->fresh('preScreening');

        return view('public.prescreen', [
            'candidate' => $candidate,
            'company' => Setting::get('company_name', 'McCrory Center'),
            'token' => $token,
            'existing' => $candidate->preScreening,
            'submitted' => true,
        ]);
    }

    public function showEmploymentApplication(string $token): RedirectResponse
    {
        return redirect()->route('public.prescreen', ['token' => $token, 'step' => 2]);
    }

    public function submitEmploymentApplication(StoreEmploymentApplicationRequest $request, string $token): RedirectResponse
    {
        $candidate = $this->resolveCandidateByPrescreenToken($token);
        $applicationData = $request->employmentApplicationData();

        $preScreening = $candidate->preScreening;

        if ($preScreening) {
            $preScreening->update([
                'employment_application_data' => $applicationData,
                'employment_application_submitted_at' => now(),
            ]);
        } else {
            $candidate->preScreening()->create([
                'education_level' => 'not_provided',
                'years_experience' => 0,
                'availability' => 'not_provided',
                'screened_by' => null,
                'employment_application_data' => $applicationData,
                'employment_application_submitted_at' => now(),
            ]);
        }

        $candidate->activityLogs()->create([
            'action' => 'employment_application_submitted',
            'description' => 'Candidate submitted the full employment application form.',
        ]);

        if ($candidate->status === CandidateStatus::POST_INTERVIEW_REVIEW) {
            $this->service->changeStatus($candidate, CandidateStatus::PRE_SCREENING_PASSED);
        }

        $this->createAdminNotification(
            $candidate,
            'Full Employment Application Submitted',
            "{$candidate->full_name} submitted the full candidate employment application."
        );

        return redirect()
            ->route('public.prescreen.application', ['token' => $token])
            ->with('submittedEmployment', true);
    }
}

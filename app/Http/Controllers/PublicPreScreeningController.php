<?php

namespace App\Http\Controllers;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\Setting;
use App\Notifications\AdminActivityNotification;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class PublicPreScreeningController extends Controller
{
    public function __construct(protected CandidateService $service) {}
    public function show(string $token): View
    {
        $candidate = Candidate::where('prescreen_token', $token)
            ->whereIn('status', [
                CandidateStatus::POST_INTERVIEW_REVIEW->value,
                CandidateStatus::PRE_SCREENING_PASSED->value,
                CandidateStatus::AWAITING_BACKGROUND_CHECK->value,
            ])
            ->firstOrFail();

        return view('public.prescreen', [
            'candidate' => $candidate,
            'company' => Setting::get('company_name', 'McCrory Center'),
            'token' => $token,
            'existing' => $candidate->preScreening,
        ]);
    }

    public function submit(Request $request, string $token): View
    {
        $candidate = Candidate::where('prescreen_token', $token)
            ->whereIn('status', [
                CandidateStatus::POST_INTERVIEW_REVIEW->value,
                CandidateStatus::PRE_SCREENING_PASSED->value,
                CandidateStatus::AWAITING_BACKGROUND_CHECK->value,
            ])
            ->firstOrFail();

        $data = $request->validate([
            'education_level' => 'required|string|max:100',
            'years_experience' => 'required|integer|min:0|max:60',
            'licenses' => 'nullable|string|max:255',
            'availability' => 'required|string|max:100',
            'earliest_start_date' => 'nullable|date',
            'additional_notes' => 'nullable|string|max:2000',
            'uploaded_form' => 'required|file|mimetypes:application/pdf|max:10240',
        ]);

        $file = $request->file('uploaded_form');
        $relativeDir = "prescreenings/{$candidate->id}";
        $absoluteDir = public_path($relativeDir);

        if (! File::exists($absoluteDir)) {
            File::makeDirectory($absoluteDir, 0755, true, true);
        }

        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
        $file->move($absoluteDir, $filename);

        $payload = array_merge(
            collect($data)->except('uploaded_form')->all(),
            [
                'screened_by' => null,
                'uploaded_form_path' => "{$relativeDir}/{$filename}",
                'uploaded_form_name' => $file->getClientOriginalName(),
            ]
        );

        if ($candidate->preScreening) {
            if ($candidate->preScreening->uploaded_form_path) {
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

        // Auto-advance to pre_screening_passed so candidate appears on the Screening page
        if ($candidate->status === CandidateStatus::POST_INTERVIEW_REVIEW) {
            $this->service->changeStatus($candidate, CandidateStatus::PRE_SCREENING_PASSED);
        }

        $notification = new AdminActivityNotification(
            '📝 Candidate Application Submitted',
            "{$candidate->full_name} submitted the post-interview application form.",
            'pre_screening_submitted',
            $candidate->id,
            $candidate->full_name
        );

        \App\Models\User::where('role', 'admin')
            ->where('is_active', true)
            ->get()
            ->each(fn ($admin) => $admin->notify($notification));

        $candidate = $candidate->fresh('preScreening');

        return view('public.prescreen', [
            'candidate' => $candidate,
            'company' => Setting::get('company_name', 'McCrory Center'),
            'token' => $token,
            'existing' => $candidate->preScreening,
            'submitted' => true,
        ]);
    }
}

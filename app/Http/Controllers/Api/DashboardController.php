<?php

namespace App\Http\Controllers\Api;

use App\Enums\CandidateStatus;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Employee;
use App\Models\Interview;
use App\Models\TimeOffRequest;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $pipeline = [];
        foreach (CandidateStatus::cases() as $s) {
            $pipeline[$s->value] = Candidate::where('status', $s)->count();
        }

        $onboardingStages = [
            'pre_onboard_documents', 'compliance_agreements', 'clinical_staff_documents',
            'emergency_contact', 'training_and_development', 'financial_and_payroll_information',
            'post_offer_documents', 'dwc_trainings', 'additional', 'job_description_letter',
        ];

        return response()->json([
            'stats' => [
                'needs_review'    => ($pipeline['hiring'] ?? 0) + ($pipeline['pre_interview_questions'] ?? 0),
                'total_pipeline'  => Candidate::inPipeline()->count(),
                'interviews'      => Interview::where('status','scheduled')->where('scheduled_at','>=',now())->count(),
                'offers_pending'  => $pipeline['offer_letter'] ?? 0,
                'onboarding'      => array_sum(array_map(fn ($k) => $pipeline[$k] ?? 0, $onboardingStages)),
                'total_employees' => Employee::where('is_active', true)->count(),
                'pending_timeoff' => TimeOffRequest::where('status','pending')->count(),
            ],
            'pipeline'          => $pipeline,
            'recent_candidates' => Candidate::with(['category','assignedTo'])->latest()->limit(8)->get(),
            'upcoming_interviews' => Interview::with('candidate')
                ->where('status','scheduled')->where('scheduled_at','>=',now())
                ->orderBy('scheduled_at')->limit(5)->get(),
        ]);
    }
}

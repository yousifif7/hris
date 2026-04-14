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

        return response()->json([
            'stats' => [
                'needs_review'    => ($pipeline['needs_review'] ?? 0) + ($pipeline['post_interview_review'] ?? 0),
                'total_pipeline'  => Candidate::inPipeline()->count(),
                'interviews'      => Interview::where('status','scheduled')->where('scheduled_at','>=',now())->count(),
                'offers_pending'  => $pipeline['offer_sent'] ?? 0,
                'onboarding'      => ($pipeline['onboarding'] ?? 0) + ($pipeline['offer_accepted'] ?? 0),
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

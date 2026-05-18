<?php

namespace App\Http\Controllers\Api;

use App\Enums\CandidateStatus;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Interview;
use App\Services\CandidateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function __construct(protected CandidateService $candidateService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Interview::with(['candidate.category', 'interviewer']);

        if ($request->get('status') === 'upcoming') {
            $query->where('status', 'scheduled')->where('scheduled_at', '>=', now());
        } elseif ($request->get('status') === 'past') {
            $query->where(fn($q) => $q->where('status', 'completed')->orWhere('scheduled_at', '<', now()));
        }

        return response()->json($query->orderBy('scheduled_at')->paginate(25));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'candidate_id'     => 'required|exists:candidates,id',
            'interviewer_id'   => 'nullable|exists:users,id',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'nullable|integer|min:5|max:120',
            'type'             => 'nullable|in:zoom,in_person,phone',
            'meeting_link'     => 'nullable|url',
        ]);

        $interview = Interview::create($data);

        // Booking an interview keeps the candidate in the Pre-Screening stage —
        // they only advance to Pre-Interview Questions once the interview is marked complete.

        return response()->json($interview->load(['candidate', 'interviewer']), 201);
    }

    /**
     * POST /api/public/interviews/book
     * Allow a candidate to self-book an interview via their scheduling link.
     */
    public function publicBook(Request $request): JsonResponse
    {
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'scheduled_at' => 'required|date|after:now',
            'meeting_link' => 'nullable|url',
        ]);

        $candidate = Candidate::findOrFail($data['candidate_id']);

        $interview = Interview::create([
            'candidate_id'     => $candidate->id,
            'scheduled_at'     => $data['scheduled_at'],
            'meeting_link'     => $data['meeting_link'] ?? null,
            'duration_minutes' => 20,
            'type'             => 'zoom',
            'status'           => 'scheduled',
        ]);

        // Self-booking keeps the candidate in Pre-Screening; the interview itself is the signal.

        return response()->json([
            'message'   => 'Interview booked successfully.',
            'interview' => $interview->load('candidate'),
        ], 201);
    }

    public function update(Request $request, Interview $interview): JsonResponse
    {
        $data = $request->validate([
            'scheduled_at'     => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:5|max:120',
            'type'             => 'nullable|in:zoom,in_person,phone',
            'meeting_link'     => 'nullable|url',
            'notes'            => 'nullable|string',
            'status'           => 'nullable|in:scheduled,completed,cancelled,no_show',
        ]);

        $interview->update(array_filter($data, fn($v) => $v !== null));
        return response()->json($interview->fresh(['candidate', 'interviewer']));
    }

    public function destroy(Interview $interview): JsonResponse
    {
        $interview->delete();
        return response()->json(['ok' => true]);
    }

    /**
     * GET /api/portal/interviews
     * Logged-in employee's own interviews, found via employee.candidate_id.
     */
    public function portalIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        $candidateId = $user->employee?->candidate_id;

        if (! $candidateId) {
            return response()->json(['data' => []]);
        }

        $interviews = Interview::with('interviewer:id,first_name,last_name')
            ->where('candidate_id', $candidateId)
            ->orderByDesc('scheduled_at')
            ->get();

        return response()->json(['data' => $interviews]);
    }

    public function complete(Request $request, Interview $interview): JsonResponse
    {
        $data = $request->validate([
            'notes'              => 'nullable|string',
            'question_responses' => 'nullable|array',
        ]);

        $interview->update(array_merge($data, ['status' => 'completed']));

        // Move candidate to Pre-Interview Questions so HR collects the post-interview application
        $this->candidateService->changeStatus(
            $interview->candidate,
            CandidateStatus::PRE_INTERVIEW_QUESTIONS
        );

        return response()->json($interview->fresh('candidate'));
    }
}

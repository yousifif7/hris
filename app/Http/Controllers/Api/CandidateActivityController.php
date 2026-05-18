<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CandidateActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CandidateActivityController extends Controller
{
    /**
     * GET /api/candidates/{candidate}/activities-v2?kind=scheduled|logged
     */
    public function index(Request $request, Candidate $candidate): JsonResponse
    {
        $request->validate([
            'kind' => ['required', Rule::in([CandidateActivity::KIND_SCHEDULED, CandidateActivity::KIND_LOGGED])],
        ]);

        $kind = $request->string('kind')->toString();

        $query = $candidate->candidateActivities()
            ->with([
                'user:id,first_name,last_name',
                'assignedUser:id,first_name,last_name',
            ])
            ->where('kind', $kind);

        if ($kind === CandidateActivity::KIND_SCHEDULED) {
            $query->orderBy('scheduled_at')->orderByDesc('id');
        } else {
            $query->orderByDesc('occurred_at')->orderByDesc('id');
        }

        return response()->json($query->get());
    }

    /**
     * POST /api/candidates/{candidate}/activities-v2
     */
    public function store(Request $request, Candidate $candidate): JsonResponse
    {
        $kind = $request->input('kind');
        $allowedTypes = $kind === CandidateActivity::KIND_LOGGED
            ? CandidateActivity::TYPES_LOGGED
            : CandidateActivity::TYPES_SCHEDULED;

        $data = $request->validate([
            'kind'             => ['required', Rule::in([CandidateActivity::KIND_SCHEDULED, CandidateActivity::KIND_LOGGED])],
            'type'             => ['required', Rule::in($allowedTypes)],
            'subject'          => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'scheduled_at'     => ['nullable', 'date'],
            'occurred_at'      => ['nullable', 'date'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if ($kind === CandidateActivity::KIND_SCHEDULED && empty($data['scheduled_at'])) {
            return response()->json(['message' => 'A scheduled date is required.'], 422);
        }
        if ($kind === CandidateActivity::KIND_LOGGED && empty($data['occurred_at'])) {
            $data['occurred_at'] = now();
        }

        $activity = $candidate->candidateActivities()->create(array_merge($data, [
            'user_id' => auth()->id(),
        ]));

        $activity->load([
            'user:id,first_name,last_name',
            'assignedUser:id,first_name,last_name',
        ]);

        return response()->json($activity, 201);
    }

    /**
     * DELETE /api/candidate-activities/{activity}
     */
    public function destroy(CandidateActivity $activity): JsonResponse
    {
        $activity->delete();
        return response()->json(['ok' => true]);
    }
}

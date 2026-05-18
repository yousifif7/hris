<?php

namespace App\Http\Controllers\Api;

use App\Enums\CandidateTaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CandidateTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CandidateTaskController extends Controller
{
    /**
     * GET /api/candidates/{candidate}/candidate-tasks
     */
    public function index(Candidate $candidate): JsonResponse
    {
        $tasks = $candidate->candidateTasks()
            ->with([
                'assignedUser:id,first_name,last_name',
                'createdBy:id,first_name,last_name',
            ])
            ->orderByDesc('id')
            ->get();

        return response()->json($tasks);
    }

    /**
     * POST /api/candidates/{candidate}/candidate-tasks
     */
    public function store(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $this->validateData($request);

        $task = $candidate->candidateTasks()->create(array_merge($data, [
            'created_by'       => auth()->id(),
            'assigned_user_id' => $data['assigned_user_id'] ?? auth()->id(),
        ]));

        $task->load([
            'assignedUser:id,first_name,last_name',
            'createdBy:id,first_name,last_name',
        ]);

        return response()->json($task, 201);
    }

    /**
     * PATCH /api/candidate-tasks/{candidateTask}
     */
    public function update(Request $request, CandidateTask $candidateTask): JsonResponse
    {
        $data = $this->validateData($request, partial: true);

        $candidateTask->update($data);
        $candidateTask->load([
            'assignedUser:id,first_name,last_name',
            'createdBy:id,first_name,last_name',
        ]);

        return response()->json($candidateTask);
    }

    /**
     * DELETE /api/candidate-tasks/{candidateTask}
     */
    public function destroy(CandidateTask $candidateTask): JsonResponse
    {
        $candidateTask->delete();
        return response()->json(['ok' => true]);
    }

    /**
     * GET /api/candidate-task-statuses
     */
    public function statuses(): JsonResponse
    {
        return response()->json(CandidateTaskStatus::options());
    }

    protected function validateData(Request $request, bool $partial = false): array
    {
        $req = $partial ? 'sometimes|' : '';
        $statusRule = Rule::in(array_column(CandidateTaskStatus::cases(), 'value'));

        return $request->validate([
            'name'                                => $req.'nullable|string|max:255',
            'assigned_user_id'                    => $req.'nullable|integer|exists:users,id',
            'status'                              => $req.'required|string|'.$statusRule,
            'evaluation_date_time'                => $req.'nullable|date',
            'review_records'                      => $req.'nullable|string|max:255',
            'was_written_verbal_consent_obtained' => $req.'nullable|string|max:255',
            'did_the_consumer_have_autism'        => $req.'nullable|string|max:255',
            'description'                         => $req.'required|string',
            'teams'                               => $req.'nullable|string|max:255',
            'quality_review'                      => $req.'nullable|string',
            'quality_assurance'                   => $req.'nullable|string',
            'report_review_status'                => $req.'nullable|string',
            'reviewer'                            => $req.'nullable|string',
            'supervisor_review'                   => $req.'nullable|string',
            'signed_report'                       => $req.'nullable|string',
        ]);
    }
}

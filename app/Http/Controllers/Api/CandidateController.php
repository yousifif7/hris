<?php

namespace App\Http\Controllers\Api;

use App\Enums\CandidateStatus;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Services\CandidateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function __construct(
        protected CandidateService $service
    ) {}

    /**
     * GET /api/candidates
     * List candidates with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $with = ['category', 'assignedTo:id,first_name,last_name'];

        // Allow callers to request extra relations (e.g. screening page needs checks/refs)
        $allowed = ['backgroundChecks', 'references', 'preScreening', 'interviews'];
        if ($request->filled('include')) {
            foreach (explode(',', $request->include) as $rel) {
                if (in_array($rel, $allowed)) {
                    $with[] = $rel;
                }
            }
        }

        $query = Candidate::with($with);

        if ($request->filled('status')) {
            $statuses = array_filter(array_map('trim', explode(',', $request->status)));
            if (count($statuses) > 1) {
                $query->whereIn('status', $statuses);
            } else {
                $query->where('status', $statuses[0]);
            }
        }

        if ($request->filled('category')) {
            $query->where('job_category_id', $request->category);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $sort = $request->get('sort', 'created_at');
        $dir  = $request->get('direction', 'desc');

        $candidates = $query->orderBy($sort, $dir)->paginate($request->get('per_page', 25));

        return response()->json($candidates);
    }

    /**
     * POST /api/candidates
     * Create a new candidate (manual entry or resume upload).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'source'          => 'required|string|max:100',
            'notes'           => 'nullable|string',
            'resume_text'     => 'nullable|string',
            'resume_file'     => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $candidate = $this->service->create(
            $validated,
            $request->file('resume_file')
        );

        return response()->json($candidate->load(['category', 'assignedTo']), 201);
    }

    /**
     * GET /api/candidates/{candidate}
     */
    public function show(Candidate $candidate): JsonResponse
    {
        $candidate->load([
            'category', 'assignedTo', 'preScreening', 'interviews.interviewer',
            'backgroundChecks', 'references', 'latestOffer', 'onboardingTasks',
            'activityLogs.user', 'documents',
        ]);

        return response()->json([
            'candidate'           => $candidate,
            'onboarding_progress' => $candidate->onboardingProgress(),
        ]);
    }

    /**
     * PUT /api/candidates/{candidate}
     */
    public function update(Request $request, Candidate $candidate): JsonResponse
    {
        $validated = $request->validate([
            'first_name'      => 'sometimes|string|max:255',
            'last_name'       => 'sometimes|string|max:255',
            'email'           => 'sometimes|nullable|email',
            'phone'           => 'sometimes|nullable|string|max:50',
            'job_category_id' => 'sometimes|nullable|exists:job_categories,id',
            'notes'           => 'sometimes|nullable|string',
            'resume_text'     => 'sometimes|nullable|string',
            'assigned_to'     => 'sometimes|nullable|exists:users,id',
        ]);

        $candidate->update($validated);

        return response()->json($candidate->fresh(['category', 'assignedTo']));
    }

    /**
     * DELETE /api/candidates/{candidate}
     */
    public function destroy(Candidate $candidate): JsonResponse
    {
        $candidate->delete();
        return response()->json(['message' => 'Candidate deleted']);
    }

    /**
     * PATCH /api/candidates/{candidate}/status
     * Change status with all automation triggers.
     */
    public function updateStatus(Request $request, Candidate $candidate): JsonResponse
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $newStatus = CandidateStatus::from($request->status);
        $candidate = $this->service->changeStatus($candidate, $newStatus, auth()->id());

        return response()->json($candidate->fresh(['category', 'assignedTo']));
    }

    /**
     * POST /api/candidates/{candidate}/advance
     * Move to next pipeline stage.
     */
    public function advance(Candidate $candidate): JsonResponse
    {
        $candidate->advanceStatus();
        return response()->json($candidate->fresh());
    }

    /**
     * GET /api/candidates/review-queue
     * Get all candidates needing review.
     */
    public function reviewQueue(): JsonResponse
    {
        $candidates = Candidate::needsReview()
            ->with(['category', 'assignedTo', 'interviews', 'preScreening'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($candidates);
    }

    /**
     * GET /api/candidates/pipeline
     * Get pipeline counts by status.
     */
    public function pipeline(): JsonResponse
    {
        $pipeline = [];
        foreach (CandidateStatus::cases() as $status) {
            $pipeline[$status->value] = [
                'label' => $status->label(),
                'count' => Candidate::where('status', $status)->count(),
            ];
        }

        return response()->json($pipeline);
    }

    /**
     * POST /api/candidates/upload-resume
     * Upload resume file(s) — creates candidates from file uploads.
     */
    public function uploadResume(Request $request): JsonResponse
    {
        $request->validate([
            'resumes'   => 'required|array|min:1',
            'resumes.*' => 'file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $created = [];

        foreach ($request->file('resumes') as $file) {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $parts = explode('_', str_replace(['-', '.'], '_', $name));

            $candidate = $this->service->create([
                'first_name' => ucfirst($parts[0] ?? 'Uploaded'),
                'last_name'  => ucfirst($parts[1] ?? 'Resume'),
                'source'     => 'Upload',
                'notes'      => "Uploaded file: {$file->getClientOriginalName()}",
            ], $file);

            $created[] = $candidate;
        }

        return response()->json([
            'message'    => count($created) . ' resume(s) uploaded',
            'candidates' => $created,
        ], 201);
    }

    /**
     * POST /api/candidates/{candidate}/convert-to-employee
     */
    public function convertToEmployee(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'department'              => 'nullable|string',
            'start_date'              => 'nullable|date',
            'access_info'             => 'nullable|array',
            'access_info.email_login' => 'nullable|string|max:255',
            'access_info.temp_password' => 'nullable|string|max:255',
            'access_info.door_code'   => 'nullable|string|max:255',
            'access_info.wifi_password' => 'nullable|string|max:255',
        ]);

        $employee = $this->service->convertToEmployee($candidate, $data);

        return response()->json($employee, 201);
    }
}

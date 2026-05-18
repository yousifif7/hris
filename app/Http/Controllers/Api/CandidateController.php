<?php

namespace App\Http\Controllers\Api;

use App\Enums\CandidateStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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
            'street_address'  => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:120',
            'state'           => 'nullable|string|max:120',
            'postal_code'     => 'nullable|string|max:20',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'source'          => 'required|string|max:100',
            'notes'           => 'nullable|string',
            'resume_text'     => 'nullable|string',
            'resume_file'     => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'linkedin_url'    => 'nullable|url|max:255',
            'years_experience' => 'nullable|integer|min:0|max:60',
            'is_authorized_to_work' => 'nullable|boolean',
            'desired_pay'     => 'nullable|numeric|min:0|max:999999.99',
            'earliest_start_date' => 'nullable|date',
            'availability'     => 'nullable|in:full_time,part_time,contract,temporary,internship,remote',
            'clinical_license_expires_at' => 'nullable|date',
            'authorization_background_check' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $candidate = $this->service->create(
            $validated,
            $request->file('resume_file'),
            $request->file('authorization_background_check')
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
            'street_address'  => 'sometimes|nullable|string|max:255',
            'city'            => 'sometimes|nullable|string|max:120',
            'state'           => 'sometimes|nullable|string|max:120',
            'postal_code'     => 'sometimes|nullable|string|max:20',
            'job_category_id' => 'sometimes|nullable|exists:job_categories,id',
            'notes'           => 'sometimes|nullable|string',
            'resume_text'     => 'sometimes|nullable|string',
            'assigned_to'     => 'sometimes|nullable|exists:users,id',
            'linkedin_url'    => 'sometimes|nullable|url|max:255',
            'years_experience' => 'sometimes|nullable|integer|min:0|max:60',
            'is_authorized_to_work' => 'sometimes|nullable|boolean',
            'desired_pay'     => 'sometimes|nullable|numeric|min:0|max:999999.99',
            'earliest_start_date' => 'sometimes|nullable|date',
            'availability'     => 'sometimes|nullable|in:full_time,part_time,contract,temporary,internship,remote',
            'clinical_license_expires_at' => 'sometimes|nullable|date',

            // Candidate detail tab fields
            'candidate_for'                  => 'sometimes|nullable|string|max:255',
            'resume_w_applicable_experience' => 'sometimes|nullable|string',
            'pre_screen_note'                => 'sometimes|nullable|string',
            'pre_screening_status'           => 'sometimes|nullable|string|max:120',
            'full_or_part_time'              => 'sometimes|nullable|string|max:50',
            'ideal_schedule'                 => 'sometimes|nullable|array',
            'ideal_schedule.*'               => 'string|max:50',
            'description'                    => 'sometimes|nullable|string',
            'days_available'                 => 'sometimes|nullable|array',
            'days_available.*'               => 'string|max:20',
            'position'                       => 'sometimes|nullable|string|max:255',
            'clinical_position_type'         => 'sometimes|nullable|string|max:120',
            'position_type'                  => 'sometimes|nullable|string|max:120',
            'staff_type'                     => 'sometimes|nullable|string|max:120',
            'background_check_expires_at'    => 'sometimes|nullable|date',
            'cpr_certification_expires_at'   => 'sometimes|nullable|date',
            'tb_expires_at'                  => 'sometimes|nullable|date',
            'pgl_insurance_expires_at'       => 'sometimes|nullable|date',
            'cmhp_hours_current_year'        => 'sometimes|nullable|numeric|min:0|max:99999.99',
            'dwc_training_progress'          => 'sometimes|nullable|integer|min:0|max:100',

            // Verification & Review
            'background_check_status'        => 'sometimes|nullable|string|max:120',
            'identification_expires_at'      => 'sometimes|nullable|date',
            'i9_verification'                => 'sometimes|nullable|array',
            'i9_verification.*'              => 'string|max:50',
            'onboarding_documents_checklist' => 'sometimes|nullable|array',
            'onboarding_documents_checklist.*' => 'string|max:255',
            'reference_1_name'               => 'sometimes|nullable|string|max:255',
            'reference_1_phone'              => 'sometimes|nullable|string|max:50',
            'reference_1_association'        => 'sometimes|nullable|string|max:255',
            'reference_2_name'               => 'sometimes|nullable|string|max:255',
            'reference_2_phone'              => 'sometimes|nullable|string|max:50',
            'reference_2_association'        => 'sometimes|nullable|string|max:255',

            // Offer Letter
            'offer_date'                     => 'sometimes|nullable|date',
            'offer_mccrory_center'           => 'sometimes|nullable|string|max:255',
            'operations_manager'             => 'sometimes|nullable|string|max:255',
            'clinical_supervisor'            => 'sometimes|nullable|string|max:255',
            'offer_amount'                   => 'sometimes|nullable|numeric|min:0|max:9999999.99',
            'payment_frequency'              => 'sometimes|nullable|string|max:50',
            'company_representative'         => 'sometimes|nullable|string|max:255',
            'offer_deadline_date'            => 'sometimes|nullable|date',

            // Pre-Onboard Documents
            'college_degree'                 => 'sometimes|nullable|string|max:255',
            'college_transcripts'            => 'sometimes|nullable|string|max:255',
            'cpr_certification'              => 'sometimes|nullable|string|max:255',
            'child_registry_clearance'       => 'sometimes|nullable|string|max:255',
            'child_registry_clearance_expires_at' => 'sometimes|nullable|date',
            'tb_test_results'                => 'sometimes|nullable|string|max:255',
            'dwihn_transcripts'              => 'sometimes|nullable|string|max:255',
            'i9_document'                    => 'sometimes|nullable|string|max:255',

            // Compliance Agreements
            'baa_agreement'                  => 'sometimes|nullable|string|max:255',
            'nda_hipaa'                      => 'sometimes|nullable|string|max:255',
            'acknowledgement_handbook'       => 'sometimes|boolean',

            // Clinical Staff Documents
            'professional_general_liability_insurance' => 'sometimes|nullable|string|max:255',
            'clinical_licenses'              => 'sometimes|nullable|string|max:255',
            'medversant_application_confirmation' => 'sometimes|nullable|string|max:255',
            'writing_sample'                 => 'sometimes|nullable|string|max:255',

            // Emergency Contact
            'emergency_contact_1_name'       => 'sometimes|nullable|string|max:255',
            'emergency_contact_1_phone'      => 'sometimes|nullable|string|max:50',
            'emergency_contact_2_name'       => 'sometimes|nullable|string|max:255',
            'emergency_contact_2_phone'      => 'sometimes|nullable|string|max:50',

            // Training and Development
            'recipient_rights_training_expires_at' => 'sometimes|nullable|date',
            'handbook'                       => 'sometimes|nullable|string|max:255',

            // DWC Trainings
            'dwc_transcript'                       => 'sometimes|nullable|string|max:255',
            'dwc_abuse_neglect_status'             => 'sometimes|nullable|string|max:50',
            'dwc_abuse_neglect_expires_at'         => 'sometimes|nullable|date',
            'dwc_anti_harassment_status'           => 'sometimes|nullable|string|max:50',
            'dwc_anti_harassment_expires_at'       => 'sometimes|nullable|date',
            'dwc_cultural_competence_status'       => 'sometimes|nullable|string|max:50',
            'dwc_cultural_competence_expires_at'   => 'sometimes|nullable|date',
            'dwc_emergency_preparedness_status'    => 'sometimes|nullable|string|max:50',
            'dwc_emergency_preparedness_expires_at'=> 'sometimes|nullable|date',
            'dwc_grievances_status'                => 'sometimes|nullable|string|max:50',
            'dwc_grievances_expires_at'            => 'sometimes|nullable|date',
            'dwc_hipaa_basics_status'              => 'sometimes|nullable|string|max:50',
            'dwc_hipaa_basics_expires_at'          => 'sometimes|nullable|date',
            'dwc_sex_trafficking_status'           => 'sometimes|nullable|string|max:50',
            'dwc_sex_trafficking_expires_at'       => 'sometimes|nullable|date',
            'dwc_infection_prevention_status'      => 'sometimes|nullable|string|max:50',
            'dwc_infection_prevention_expires_at'  => 'sometimes|nullable|date',
            'dwc_lep_status'                       => 'sometimes|nullable|string|max:50',
            'dwc_lep_expires_at'                   => 'sometimes|nullable|date',
            'dwc_medicare_compliance_status'       => 'sometimes|nullable|string|max:50',
            'dwc_medicare_compliance_expires_at'   => 'sometimes|nullable|date',
            'dwc_medicare_fraud_status'            => 'sometimes|nullable|string|max:50',
            'dwc_medicare_fraud_expires_at'        => 'sometimes|nullable|date',
            'dwc_person_centered_status'           => 'sometimes|nullable|string|max:50',
            'dwc_person_centered_expires_at'       => 'sometimes|nullable|date',
            'dwc_recipient_rights_status'          => 'sometimes|nullable|string|max:50',
            'dwc_recipient_rights_expires_at'      => 'sometimes|nullable|date',
        ]);

        if (auth()->id()) {
            $validated['last_modified_by'] = auth()->id();
        }

        // Capture before-state for the fields we surface in the stream so we can log only real changes.
        $before = $candidate->only(array_keys($validated));

        $candidate->update($validated);

        $this->logMeaningfulFieldChanges($candidate, $before, $validated);

        return response()->json($candidate->fresh(['category', 'assignedTo', 'lastModifiedBy']));
    }

    /**
     * Write ActivityLog rows for meaningful candidate field changes so they appear
     * in the Stream. Only status, assignment, document, and expiration fields qualify.
     */
    protected function logMeaningfulFieldChanges(Candidate $candidate, array $before, array $after): void
    {
        $documentFields = [
            'college_degree', 'college_transcripts', 'cpr_certification',
            'child_registry_clearance', 'tb_test_results', 'dwihn_transcripts', 'i9_document',
            'baa_agreement', 'nda_hipaa',
            'professional_general_liability_insurance', 'clinical_licenses',
            'medversant_application_confirmation', 'writing_sample', 'handbook',
            'dwc_transcript', 'signed_application_path', 'authorization_background_check_path',
            'resume_file',
        ];

        foreach ($after as $field => $newValue) {
            $oldValue = $before[$field] ?? null;
            if ($oldValue == $newValue) continue;

            $isExpiration = str_ends_with($field, '_expires_at');
            $isAssignment = $field === 'assigned_to';
            $isDocument   = in_array($field, $documentFields, true);

            if (! ($isExpiration || $isAssignment || $isDocument)) {
                continue;
            }

            $label = $this->fieldLabel($field);
            $candidate->activityLogs()->create([
                'user_id'     => auth()->id(),
                'action'      => 'field_changed',
                'old_value'   => is_scalar($oldValue) ? (string) $oldValue : null,
                'new_value'   => is_scalar($newValue) ? (string) $newValue : null,
                'description' => $label,
            ]);
        }
    }

    protected function fieldLabel(string $field): string
    {
        // Strip trailing _path / _name / _expires_at for nicer labels
        $base = preg_replace('/_(path|name|expires_at)$/', '', $field);
        return ucwords(str_replace('_', ' ', $base));
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
     * GET /api/candidates-new
     * Backs the "Pre-Screening" sidebar page — the HR inbox of unreviewed candidates
     * (default Hiring status) plus anyone actively in Pre-Screening.
     */
    public function newCandidates(): JsonResponse
    {
        $candidates = Candidate::whereIn('status', [
                CandidateStatus::HIRING,
                CandidateStatus::PRE_SCREENING,
            ])
            ->with(['category', 'assignedTo', 'preScreening'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($candidates);
    }

    /**
     * GET /api/candidates-review-queue
     * Backs the "Pre-Interview Questions" sidebar page.
     */
    public function reviewQueue(): JsonResponse
    {
        $candidates = Candidate::where('status', CandidateStatus::PRE_INTERVIEW_QUESTIONS)
            ->with(['category', 'assignedTo', 'interviews', 'preScreening'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($candidates);
    }

    /**
     * GET /api/candidates-pending-review
     * Convenience endpoint: anything HR still owes a manual review on
     * (fresh Hiring intake + post-interview applications).
     */
    public function pendingReview(): JsonResponse
    {
        $candidates = Candidate::whereIn('status', [
                CandidateStatus::HIRING,
                CandidateStatus::PRE_INTERVIEW_QUESTIONS,
            ])
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
     * GET /api/candidates/{candidate}/comments
     * Comments are stored as ActivityLog rows with action='comment'.
     */
    public function listComments(Candidate $candidate): JsonResponse
    {
        // Stream now includes user comments AND meaningful auto-events
        // (status changes, assignment, document uploads, expirations).
        $rows = $candidate->activityLogs()
            ->whereIn('action', ['comment', 'status_changed', 'field_changed', 'created'])
            ->with('user:id,first_name,last_name')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return response()->json($rows);
    }

    /**
     * POST /api/candidates/{candidate}/comments
     */
    public function addComment(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $log = $candidate->activityLogs()->create([
            'user_id'     => auth()->id(),
            'action'      => 'comment',
            'description' => $data['body'],
        ]);

        return response()->json($log->load('user:id,first_name,last_name'), 201);
    }

    /**
     * POST /api/candidates/{candidate}/upload
     * Generic per-field document upload (used by tabs that need file storage,
     * e.g. Training tab Recipient Rights / Annual CEUs).
     */
    public function uploadDocument(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'field' => 'required|string|in:recipient_rights_training,annual_ceus,college_degree,college_transcripts,cpr_certification,child_registry_clearance,tb_test_results,dwihn_transcripts,i9_document',
            'file'  => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('candidate-documents/'.$candidate->id, 'public');

        // Most fields use <field>_path + <field>_name. The pre-onboard fields reuse
        // the existing string column as the path and add a separate _name column.
        $candidate->update([
            $data['field'].'_path' => $path,
            $data['field'].'_name' => $file->getClientOriginalName(),
            'last_modified_by'     => auth()->id(),
        ]);

        $candidate->activityLogs()->create([
            'user_id'     => auth()->id(),
            'action'      => 'field_changed',
            'description' => $this->fieldLabel($data['field']),
            'new_value'   => $file->getClientOriginalName(),
        ]);

        return response()->json([
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'url'  => '/storage/'.$path,
        ]);
    }

    /**
     * POST /api/candidates/{candidate}/duplicate
     */
    public function duplicate(Candidate $candidate): JsonResponse
    {
        $copy = $candidate->replicate([
            'invite_sent_at', 'schedule_token', 'prescreen_token',
            'last_followup_at', 'followup_count',
        ]);
        $copy->first_name = $candidate->first_name.' (Copy)';
        $copy->status = CandidateStatus::HIRING;
        $copy->email = null;        // avoid uniqueness collisions if any
        $copy->phone = null;
        $copy->push();

        return response()->json($copy, 201);
    }

    /**
     * GET /api/candidates/{candidate}/activities
     * Recent activity log entries (non-comment), used by the sidebar widget.
     */
    public function listActivities(Candidate $candidate): JsonResponse
    {
        $rows = $candidate->activityLogs()
            ->where('action', '!=', 'comment')
            ->with('user:id,first_name,last_name')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json($rows);
    }

    /**
     * POST /api/candidates/{candidate}/activities
     * Manually logged activity (email/meeting/phone/note) from the sidebar.
     */
    public function addActivity(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'type'        => 'required|string|in:email,meeting,phone,note',
            'description' => 'required|string|max:5000',
        ]);

        $log = $candidate->activityLogs()->create([
            'user_id'     => auth()->id(),
            'action'      => $data['type'],
            'description' => $data['description'],
        ]);

        return response()->json($log->load('user:id,first_name,last_name'), 201);
    }

    /**
     * GET /api/candidates/{candidate}/tasks
     * Onboarding tasks for the sidebar Tasks widget.
     */
    public function listTasks(Candidate $candidate): JsonResponse
    {
        return response()->json(
            $candidate->onboardingTasks()->orderBy('sort_order')->orderBy('id')->get()
        );
    }

    /**
     * POST /api/candidates/{candidate}/tasks
     * Quick-add a single task from the sidebar.
     */
    public function addTask(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'task_name' => 'required|string|max:255',
        ]);

        $maxSort = (int) ($candidate->onboardingTasks()->max('sort_order') ?? 0);

        $task = $candidate->onboardingTasks()->create([
            'task_name'    => $data['task_name'],
            'sort_order'   => $maxSort + 1,
            'is_completed' => false,
        ]);

        return response()->json($task, 201);
    }

    /**
     * GET /api/candidates/{candidate}/audit-log
     */
    public function auditLog(Candidate $candidate): JsonResponse
    {
        $logs = $candidate->activityLogs()
            ->with('user:id,first_name,last_name')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return response()->json($logs);
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

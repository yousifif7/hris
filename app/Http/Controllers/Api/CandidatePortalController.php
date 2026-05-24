<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CandidatePortalController extends Controller
{
    /**
     * Fields the logged-in candidate is allowed to edit on themselves.
     * Anything not in this list is read-only from the portal — including
     * assignments, status, offer terms, supervisors, pay, dates set by HR.
     */
    protected const EDITABLE_FIELDS = [
        // Personal
        'first_name', 'last_name', 'phone',
        'street_address', 'city', 'state', 'postal_code',
        // Emergency contacts
        'emergency_contact_1_name', 'emergency_contact_1_phone',
        'emergency_contact_2_name', 'emergency_contact_2_phone',
        // References
        'reference_1_name', 'reference_1_phone', 'reference_1_association',
        'reference_2_name', 'reference_2_phone', 'reference_2_association',
        // Compliance acknowledgements (read-and-agree)
        'acknowledgement_handbook',
    ];

    /**
     * Document fields the candidate is allowed to upload to from the portal.
     */
    protected const UPLOADABLE_DOC_FIELDS = [
        'college_degree', 'college_transcripts', 'cpr_certification',
        'child_registry_clearance', 'tb_test_results', 'dwihn_transcripts',
        'i9_document', 'recipient_rights_training', 'annual_ceus',
    ];

    protected function candidate(Request $request): Candidate
    {
        $candidate = $request->user()?->candidate;
        abort_unless($candidate, 404, 'No candidate record linked to this account.');
        return $candidate->load(['assignedTo:id,first_name,last_name,email', 'latestOffer', 'category']);
    }

    /**
     * GET /api/candidate-portal/me
     * Returns the candidate, the editable allowlist, and HR-set read-only data.
     */
    public function me(Request $request): JsonResponse
    {
        $candidate = $this->candidate($request);

        return response()->json([
            'candidate'       => $candidate,
            'editable_fields' => self::EDITABLE_FIELDS,
            'document_fields' => self::UPLOADABLE_DOC_FIELDS,
            'read_only'       => [
                'status'              => $candidate->status?->value,
                'status_label'        => $candidate->status?->label(),
                'assigned_to'         => $candidate->assignedTo
                    ? trim($candidate->assignedTo->first_name.' '.$candidate->assignedTo->last_name)
                    : null,
                'operations_manager'  => $candidate->operations_manager,
                'clinical_supervisor' => $candidate->clinical_supervisor,
                'company_representative' => $candidate->company_representative,
                'offer' => [
                    'amount'        => $candidate->offer_amount,
                    'frequency'     => $candidate->payment_frequency,
                    'start_date'    => $candidate->earliest_start_date,
                    'deadline_date' => $candidate->offer_deadline_date,
                ],
            ],
        ]);
    }

    /**
     * PUT /api/candidate-portal/me
     * Update only allowlisted fields on the candidate's own record.
     */
    public function update(Request $request): JsonResponse
    {
        $candidate = $this->candidate($request);

        $validated = $request->validate([
            'first_name'                 => 'sometimes|string|max:255',
            'last_name'                  => 'sometimes|string|max:255',
            'phone'                      => 'sometimes|nullable|string|max:50',
            'street_address'             => 'sometimes|nullable|string|max:255',
            'city'                       => 'sometimes|nullable|string|max:120',
            'state'                      => 'sometimes|nullable|string|max:120',
            'postal_code'                => 'sometimes|nullable|string|max:20',
            'emergency_contact_1_name'   => 'sometimes|nullable|string|max:255',
            'emergency_contact_1_phone'  => 'sometimes|nullable|string|max:50',
            'emergency_contact_2_name'   => 'sometimes|nullable|string|max:255',
            'emergency_contact_2_phone'  => 'sometimes|nullable|string|max:50',
            'reference_1_name'           => 'sometimes|nullable|string|max:255',
            'reference_1_phone'          => 'sometimes|nullable|string|max:50',
            'reference_1_association'    => 'sometimes|nullable|string|max:255',
            'reference_2_name'           => 'sometimes|nullable|string|max:255',
            'reference_2_phone'          => 'sometimes|nullable|string|max:50',
            'reference_2_association'    => 'sometimes|nullable|string|max:255',
            'acknowledgement_handbook'   => 'sometimes|boolean',
        ]);

        // Belt-and-suspenders: strip anything outside the editable allowlist even if
        // the validator was somehow bypassed by a future change.
        $safe = array_intersect_key($validated, array_flip(self::EDITABLE_FIELDS));

        $candidate->fill($safe)->save();

        $candidate->activityLogs()->create([
            'user_id'     => $request->user()->id,
            'action'      => 'candidate_self_update',
            'description' => 'Candidate updated their profile from the Candidate Portal.',
        ]);

        return response()->json($candidate->fresh());
    }

    /**
     * POST /api/candidate-portal/upload
     * Upload a document into an allowlisted field on the candidate's own record.
     */
    public function upload(Request $request): JsonResponse
    {
        $candidate = $this->candidate($request);

        $data = $request->validate([
            'field' => ['required', 'string', 'in:'.implode(',', self::UPLOADABLE_DOC_FIELDS)],
            'file'  => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('candidate-documents/'.$candidate->id, 'public');

        $candidate->update([
            $data['field'].'_path' => $path,
            $data['field'].'_name' => $file->getClientOriginalName(),
        ]);

        $candidate->activityLogs()->create([
            'user_id'     => $request->user()->id,
            'action'      => 'candidate_self_upload',
            'description' => 'Candidate uploaded '.$data['field'].' from the portal.',
            'new_value'   => $file->getClientOriginalName(),
        ]);

        return response()->json([
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'url'  => '/storage/'.$path,
        ]);
    }

    /**
     * POST /api/candidate-portal/change-password
     * Lets the candidate set a new password (typically after first sign-in with their temp).
     */
    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update(['password' => Hash::make($data['new_password'])]);

        return response()->json(['message' => 'Password updated.']);
    }
}

<?php

namespace App\Models;

use App\Enums\CandidateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'street_address', 'city', 'state', 'postal_code',
        'job_category_id', 'source', 'status', 'assigned_to', 'last_modified_by',
        'notes', 'resume_text', 'resume_file',
        'linkedin_url', 'years_experience', 'education_level', 'is_authorized_to_work', 'desired_pay', 'earliest_start_date', 'availability',
        'clinical_license_expires_at', 'authorization_background_check_path', 'authorization_background_check_name',
        // Candidate-detail tab fields
        'candidate_for', 'resume_w_applicable_experience', 'pre_screen_note', 'pre_screening_status',
        'full_or_part_time', 'ideal_schedule', 'description', 'days_available',
        'position', 'clinical_position_type', 'position_type', 'staff_type',
        'signed_application_path', 'signed_application_name',
        'background_check_expires_at', 'cpr_certification_expires_at', 'tb_expires_at',
        'pgl_insurance_expires_at', 'cmhp_hours_current_year', 'dwc_training_progress',
        // Verification & Review tab
        'background_check_status', 'identification_expires_at',
        'i9_verification', 'onboarding_documents_checklist',
        'reference_1_name', 'reference_1_phone', 'reference_1_association',
        'reference_2_name', 'reference_2_phone', 'reference_2_association',
        // Offer Letter tab
        'offer_date', 'offer_mccrory_center', 'operations_manager',
        'clinical_supervisor', 'offer_amount', 'payment_frequency',
        'company_representative', 'offer_deadline_date',
        // Pre-Onboard Documents tab
        'college_degree', 'college_degree_name',
        'college_transcripts', 'college_transcripts_name',
        'cpr_certification', 'cpr_certification_name',
        'child_registry_clearance', 'child_registry_clearance_name',
        'child_registry_clearance_expires_at',
        'tb_test_results', 'tb_test_results_name',
        'dwihn_transcripts', 'dwihn_transcripts_name',
        'i9_document', 'i9_document_name',
        // Compliance Agreements tab
        'baa_agreement', 'nda_hipaa', 'acknowledgement_handbook',
        // Clinical Staff Documents tab
        'professional_general_liability_insurance', 'clinical_licenses',
        'medversant_application_confirmation', 'writing_sample',
        // Emergency Contact tab
        'emergency_contact_1_name', 'emergency_contact_1_phone',
        'emergency_contact_2_name', 'emergency_contact_2_phone',
        // Training and Development tab
        'recipient_rights_training_path', 'recipient_rights_training_name',
        'recipient_rights_training_expires_at', 'handbook',
        'annual_ceus_path', 'annual_ceus_name',
        // DWC Trainings tab
        'dwc_transcript',
        'dwc_abuse_neglect_status', 'dwc_abuse_neglect_expires_at',
        'dwc_anti_harassment_status', 'dwc_anti_harassment_expires_at',
        'dwc_cultural_competence_status', 'dwc_cultural_competence_expires_at',
        'dwc_emergency_preparedness_status', 'dwc_emergency_preparedness_expires_at',
        'dwc_grievances_status', 'dwc_grievances_expires_at',
        'dwc_hipaa_basics_status', 'dwc_hipaa_basics_expires_at',
        'dwc_sex_trafficking_status', 'dwc_sex_trafficking_expires_at',
        'dwc_infection_prevention_status', 'dwc_infection_prevention_expires_at',
        'dwc_lep_status', 'dwc_lep_expires_at',
        'dwc_medicare_compliance_status', 'dwc_medicare_compliance_expires_at',
        'dwc_medicare_fraud_status', 'dwc_medicare_fraud_expires_at',
        'dwc_person_centered_status', 'dwc_person_centered_expires_at',
        'dwc_recipient_rights_status', 'dwc_recipient_rights_expires_at',
        'invite_sent_at', 'schedule_token', 'prescreen_token', 'last_followup_at', 'followup_count',
    ];

    protected function casts(): array
    {
        return [
            'status'                        => CandidateStatus::class,
            'invite_sent_at'                => 'datetime',
            'last_followup_at'              => 'datetime',
            'is_authorized_to_work'         => 'boolean',
            'earliest_start_date'           => 'date',
            'clinical_license_expires_at'   => 'date',
            'background_check_expires_at'   => 'date',
            'cpr_certification_expires_at'  => 'date',
            'tb_expires_at'                 => 'date',
            'pgl_insurance_expires_at'      => 'date',
            'ideal_schedule'                => 'array',
            'days_available'                => 'array',
            'cmhp_hours_current_year'       => 'decimal:2',
            'i9_verification'               => 'array',
            'onboarding_documents_checklist'=> 'array',
            'identification_expires_at'     => 'date',
            'offer_date'                    => 'date',
            'offer_amount'                  => 'decimal:2',
            'offer_deadline_date'           => 'date',
            'child_registry_clearance_expires_at' => 'date',
            'acknowledgement_handbook'      => 'boolean',
            'recipient_rights_training_expires_at' => 'date',
            'dwc_abuse_neglect_expires_at'         => 'date',
            'dwc_anti_harassment_expires_at'       => 'date',
            'dwc_cultural_competence_expires_at'   => 'date',
            'dwc_emergency_preparedness_expires_at'=> 'date',
            'dwc_grievances_expires_at'            => 'date',
            'dwc_hipaa_basics_expires_at'          => 'date',
            'dwc_sex_trafficking_expires_at'       => 'date',
            'dwc_infection_prevention_expires_at'  => 'date',
            'dwc_lep_expires_at'                   => 'date',
            'dwc_medicare_compliance_expires_at'   => 'date',
            'dwc_medicare_fraud_expires_at'        => 'date',
            'dwc_person_centered_expires_at'       => 'date',
            'dwc_recipient_rights_expires_at'      => 'date',
        ];
    }

    /* ── Relationships ── */

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lastModifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    public function preScreening(): HasOne
    {
        return $this->hasOne(PreScreening::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function interviewAvailabilitySlots(): HasMany
    {
        return $this->hasMany(InterviewAvailabilitySlot::class);
    }

    public function backgroundChecks(): HasMany
    {
        return $this->hasMany(BackgroundCheck::class);
    }

    public function references(): HasMany
    {
        return $this->hasMany(CandidateReference::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function latestOffer(): HasOne
    {
        return $this->hasOne(Offer::class)->latestOfMany();
    }

    public function onboardingTasks(): HasMany
    {
        return $this->hasMany(OnboardingTask::class);
    }

    public function candidateTasks(): HasMany
    {
        return $this->hasMany(CandidateTask::class);
    }

    public function candidateActivities(): HasMany
    {
        return $this->hasMany(CandidateActivity::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    /* ── Accessors ── */

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /* ── Scopes ── */

    public function scopeNeedsReview($query)
    {
        return $query->whereIn('status', [
            CandidateStatus::HIRING,
            CandidateStatus::PRE_INTERVIEW_QUESTIONS,
        ]);
    }

    public function scopeInPipeline($query)
    {
        return $query->whereNotIn('status', [
            CandidateStatus::REJECTED,
            CandidateStatus::APPLICANT_DECLINED,
            CandidateStatus::HIRED,
        ]);
    }

    /* ── Business Logic ── */

    public function advanceStatus(): void
    {
        $flow = CandidateStatus::sequence();
        $idx = array_search($this->status, $flow, true);
        if ($idx !== false && $idx < count($flow) - 1) {
            $this->update(['status' => $flow[$idx + 1]]);
        }
    }

    public function allBackgroundChecksComplete(): bool
    {
        foreach (['mdhhs', 'sam_oig', 'npdb'] as $type) {
            $check = $this->backgroundChecks()->where('check_type', $type)->first();
            if (! $check || $check->status !== 'complete') return false;
        }
        return true;
    }

    public function onboardingProgress(): array
    {
        $tasks = $this->onboardingTasks;
        $total = $tasks->count();
        $done  = $tasks->where('is_completed', true)->count();

        return [
            'total'     => $total,
            'completed' => $done,
            'percent'   => $total > 0 ? round(($done / $total) * 100) : 0,
        ];
    }
}

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
        'job_category_id', 'source', 'status', 'assigned_to',
        'notes', 'resume_text', 'resume_file',
        'linkedin_url', 'years_experience', 'education_level', 'is_authorized_to_work', 'desired_pay', 'earliest_start_date',
        'invite_sent_at', 'schedule_token', 'prescreen_token', 'last_followup_at', 'followup_count',
    ];

    protected function casts(): array
    {
        return [
            'status'           => CandidateStatus::class,
            'invite_sent_at'   => 'datetime',
            'last_followup_at' => 'datetime',
            'is_authorized_to_work' => 'boolean',
            'earliest_start_date' => 'date',
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
            CandidateStatus::NEEDS_REVIEW,
            CandidateStatus::POST_INTERVIEW_REVIEW,
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
        $flow = [
            CandidateStatus::NEEDS_REVIEW,
            CandidateStatus::INVITE_SENT,
            CandidateStatus::INTERVIEW_SCHEDULED,
            CandidateStatus::POST_INTERVIEW_REVIEW,
            CandidateStatus::PRE_SCREENING_PASSED,
            CandidateStatus::AWAITING_BACKGROUND_CHECK,
            CandidateStatus::OFFER_SENT,
            CandidateStatus::OFFER_ACCEPTED,
            CandidateStatus::ONBOARDING,
            CandidateStatus::HIRED,
        ];

        $idx = array_search($this->status, $flow);
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

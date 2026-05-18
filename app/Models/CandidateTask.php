<?php

namespace App\Models;

use App\Enums\CandidateTaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateTask extends Model
{
    protected $fillable = [
        'name',
        'candidate_id',
        'assigned_user_id',
        'created_by',
        'status',
        'evaluation_date_time',
        'review_records',
        'was_written_verbal_consent_obtained',
        'did_the_consumer_have_autism',
        'description',
        'teams',
        'quality_review',
        'quality_assurance',
        'report_review_status',
        'reviewer',
        'supervisor_review',
        'signed_report',
    ];

    protected function casts(): array
    {
        return [
            'status'               => CandidateTaskStatus::class,
            'evaluation_date_time' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

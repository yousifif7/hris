<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateActivity extends Model
{
    public const KIND_SCHEDULED = 'scheduled';
    public const KIND_LOGGED    = 'logged';

    public const TYPES_SCHEDULED = [
        'email', 'meeting', 'call', 'due_date',
        'supervision_reminder', 're_evaluation',
    ];

    public const TYPES_LOGGED = [
        'meeting', 'call', 'email',
    ];

    public const TYPE_LABELS = [
        'meeting'              => 'Meeting',
        'call'                 => 'Call',
        'email'                => 'Email',
        'due_date'             => 'Due Date',
        'supervision_reminder' => 'Supervision Date Reminder',
        're_evaluation'        => 'Re-Evaluation',
    ];

    protected $fillable = [
        'candidate_id', 'user_id', 'assigned_user_id',
        'kind', 'type',
        'subject', 'description',
        'scheduled_at', 'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'occurred_at'  => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}

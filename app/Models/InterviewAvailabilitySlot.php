<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewAvailabilitySlot extends Model
{
    protected $fillable = [
        'candidate_id',
        'starts_at',
        'ends_at',
        'created_by',
        'booked_interview_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookedInterview(): BelongsTo
    {
        return $this->belongsTo(Interview::class, 'booked_interview_id');
    }
}

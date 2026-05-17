<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = [
        'candidate_id', 'interviewer_id', 'scheduled_at',
        'duration_minutes', 'type', 'meeting_link', 'status',
        'notes', 'question_responses',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at'       => 'datetime',
            'question_responses' => 'array',
        ];
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}

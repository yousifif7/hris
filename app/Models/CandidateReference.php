<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateReference extends Model
{
    protected $fillable = [
        'candidate_id', 'reference_name', 'reference_email', 'reference_phone',
        'relationship', 'status', 'questions_sent', 'response', 'sent_at', 'received_at',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime', 'received_at' => 'datetime'];
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}

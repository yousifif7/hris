<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreScreening extends Model
{
    protected $fillable = [
        'candidate_id', 'education_level', 'years_experience',
        'licenses', 'availability', 'earliest_start_date',
        'additional_notes', 'screened_by',
    ];

    protected function casts(): array
    {
        return ['earliest_start_date' => 'date'];
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function screenedBy()
    {
        return $this->belongsTo(User::class, 'screened_by');
    }
}

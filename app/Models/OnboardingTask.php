<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingTask extends Model
{
    protected $fillable = [
        'candidate_id', 'template_id', 'task_name',
        'is_completed', 'completed_at', 'document_path', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_completed' => 'boolean', 'completed_at' => 'datetime'];
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}

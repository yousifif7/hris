<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackgroundCheck extends Model
{
    protected $fillable = ['candidate_id', 'check_type', 'status', 'notes', 'completed_at'];

    protected function casts(): array
    {
        return ['completed_at' => 'date'];
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}

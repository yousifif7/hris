<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeOffRequest extends Model
{
    protected $fillable = [
        'employee_id', 'type', 'start_date', 'end_date',
        'days', 'notes', 'status', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'reviewed_at' => 'datetime'];
    }

    public function employee()   { return $this->belongsTo(Employee::class); }
    public function reviewedBy() { return $this->belongsTo(User::class, 'reviewed_by'); }
}

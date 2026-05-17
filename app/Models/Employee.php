<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Employee extends Model
{

    protected $fillable = [
        'user_id', 'candidate_id', 'first_name', 'last_name', 'email', 'phone',
        'role', 'employment_type', 'department', 'start_date', 'pay_rate', 'pay_type',
        'location', 'is_active', 'access_info',
    ];

    protected function casts(): array
    {
        return [
            'start_date'  => 'date',
            'pay_rate'    => 'decimal:2',
            'is_active'   => 'boolean',
            'access_info' => 'array',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function user()       { return $this->belongsTo(User::class); }
    public function candidate()  { return $this->belongsTo(Candidate::class); }
    public function trainings()  { return $this->hasMany(Training::class); }
    public function payrolls()   { return $this->hasMany(\App\Models\Payroll::class); }
    public function timeOffRequests() { return $this->hasMany(TimeOffRequest::class); }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'employee_id', 'name', 'due_date', 'completed_date', 'is_completed', 'certificate_path',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date', 'completed_date' => 'date', 'is_completed' => 'boolean'];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return ! $this->is_completed && $this->due_date?->between(now(), now()->addDays($days));
    }
}

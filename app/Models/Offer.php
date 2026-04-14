<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'candidate_id', 'pay_rate', 'pay_type', 'employment_type', 'location',
        'required_documents', 'deadline_days', 'orientation_date', 'start_date',
        'status', 'sent_at', 'responded_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'pay_rate'         => 'decimal:2',
            'orientation_date' => 'date',
            'start_date'       => 'date',
            'sent_at'          => 'datetime',
            'responded_at'     => 'datetime',
        ];
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->status === 'sent'
            && $this->sent_at?->addDays($this->deadline_days)->isPast();
    }
}

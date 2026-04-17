<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'candidate_id', 'pay_rate', 'pay_type', 'employment_type', 'location',
        'required_documents', 'deadline_days', 'orientation_date', 'start_date',
        'status', 'sent_at', 'responded_at', 'created_by', 'token', 'viewed_at', 'notes',
    ];

    protected $appends = ['public_url'];

    protected function casts(): array
    {
        return [
            'pay_rate'         => 'decimal:2',
            'orientation_date' => 'date',
            'start_date'       => 'date',
            'sent_at'          => 'datetime',
            'responded_at'     => 'datetime',
            'viewed_at'        => 'datetime',
        ];
    }

    public function getPublicUrlAttribute(): string
    {
        return $this->token ? config('app.url') . '/offer/' . $this->token : '';
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
        return in_array($this->status, ['sent', 'viewed'])
            && $this->sent_at !== null
            && $this->sent_at->addDays($this->deadline_days)->isPast();
    }
}

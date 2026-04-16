<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'type', 'folder', 'candidate_id', 'template_id', 'created_by',
        'from', 'to', 'cc', 'bcc', 'subject', 'body', 'is_read', 'is_html', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'is_html' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeEmails($query)
    {
        return $query->where('type', 'email');
    }

    public function scopeSms($query)
    {
        return $query->where('type', 'sms');
    }

    public function scopeFolder($query, string $folder)
    {
        return $query->where('folder', $folder);
    }
}

<?php

namespace App\Notifications;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCandidateAssigned extends Notification
{
    use Queueable;

    public function __construct(public Candidate $candidate) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'        => 'New Candidate Assigned',
            'message'      => "{$this->candidate->full_name} ({$this->candidate->category?->name}) needs review.",
            'candidate_id' => $this->candidate->id,
            'type'         => 'new_candidate',
        ];
    }
}

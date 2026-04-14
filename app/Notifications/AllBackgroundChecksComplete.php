<?php

namespace App\Notifications;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AllBackgroundChecksComplete extends Notification
{
    use Queueable;

    public function __construct(public Candidate $candidate) {}

    public function via($notifiable): array { return ['database']; }

    public function toArray($notifiable): array
    {
        return [
            'title'        => 'Background Checks Complete',
            'message'      => "All checks cleared for {$this->candidate->full_name}. Ready for final review.",
            'candidate_id' => $this->candidate->id,
            'type'         => 'bg_complete',
        ];
    }
}

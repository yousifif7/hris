<?php

namespace App\Notifications;

use App\Models\CandidateReference;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReferenceReceived extends Notification
{
    use Queueable;

    public function __construct(public CandidateReference $reference) {}

    public function via($notifiable): array { return ['database']; }

    public function toArray($notifiable): array
    {
        return [
            'title'        => 'Reference Received',
            'message'      => "Response from {$this->reference->reference_name} for {$this->reference->candidate->full_name}.",
            'candidate_id' => $this->reference->candidate_id,
            'type'         => 'reference_received',
        ];
    }
}

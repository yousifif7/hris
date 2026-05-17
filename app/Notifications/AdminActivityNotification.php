<?php

namespace App\Notifications;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string    $title,
        public string    $message,
        public string    $type,
        public ?int      $candidateId = null,
        public ?string   $actor = null,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'        => $this->title,
            'message'      => $this->message,
            'type'         => $this->type,
            'candidate_id' => $this->candidateId,
            'actor'        => $this->actor,
        ];
    }
}

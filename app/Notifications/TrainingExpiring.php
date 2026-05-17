<?php

namespace App\Notifications;

use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrainingExpiring extends Notification
{
    use Queueable;

    public function __construct(public Training $training) {}

    public function via($notifiable): array { return ['database']; }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'Training Expiring Soon',
            'message' => "{$this->training->name} due {$this->training->due_date->format('M d, Y')} for {$this->training->employee->full_name}.",
            'type'    => 'training_expiring',
        ];
    }
}

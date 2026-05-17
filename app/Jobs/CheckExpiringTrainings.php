<?php

namespace App\Jobs;

use App\Models\Training;
use App\Models\User;
use App\Notifications\TrainingExpiring;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckExpiringTrainings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expiring = Training::with('employee')
            ->where('is_completed', false)
            ->whereBetween('due_date', [now(), now()->addDays(30)])
            ->get();

        foreach ($expiring as $training) {
            if ($training->employee?->user_id) {
                User::find($training->employee->user_id)
                    ?->notify(new TrainingExpiring($training));
            }
        }
    }
}

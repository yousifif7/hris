<?php

namespace App\Jobs;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNoResponseFollowup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Candidate $candidate) {}

    public function handle(): void
    {
        $candidate = $this->candidate->fresh();

        // Only process if still in Invite Sent status
        if ($candidate->status !== CandidateStatus::INVITE_SENT) return;

        $followupDays = (int) Setting::get('followup_days', 5);
        $queueDays    = (int) Setting::get('queue_days', 10);
        $daysSince    = $candidate->invite_sent_at?->diffInDays(now()) ?? 0;

        if ($daysSince >= $queueDays) {
            // 10+ days → move to Queue
            $candidate->update(['status' => CandidateStatus::QUEUE]);
            $candidate->activityLogs()->create([
                'action'      => 'auto_queued',
                'description' => "Auto-moved to Queue after {$queueDays} days no response.",
            ]);
        } elseif ($daysSince >= $followupDays && $candidate->followup_count === 0) {
            // 5 days → send follow-up text/email
            SendCandidateEmail::dispatch($candidate, 'followup');
            $candidate->update([
                'followup_count'   => $candidate->followup_count + 1,
                'last_followup_at' => now(),
            ]);
            // Re-check at queue deadline
            static::dispatch($candidate)->delay(now()->addDays($followupDays));
        }
    }
}

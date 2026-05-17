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
use Illuminate\Support\Facades\Log;

class ProcessNoResponseFollowup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Candidate $candidate) {}

    public function handle(): void
    {
        $candidate = $this->candidate->fresh();

        // Only chase candidates still sitting in Pre-Screening with an outstanding invite
        if ($candidate->status !== CandidateStatus::PRE_SCREENING) return;
        if ($candidate->interviews()->where('status', 'scheduled')->exists()) return;

        $followupDays = (int) Setting::get('followup_days', 5);
        $daysSince    = $candidate->invite_sent_at?->diffInDays(now()) ?? 0;

        if ($daysSince >= $followupDays && $candidate->followup_count === 0) {
            if ($candidate->phone) {
                SendCandidateSms::dispatchSync($candidate, 'sms_followup');
            } else {
                Log::info("[ProcessNoResponseFollowup] Candidate #{$candidate->id} has no phone — sending follow-up email instead.");
                SendCandidateEmail::dispatchSync($candidate, 'followup');
            }
            $candidate->update([
                'followup_count'   => $candidate->followup_count + 1,
                'last_followup_at' => now(),
            ]);
        }
    }
}

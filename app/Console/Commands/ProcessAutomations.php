<?php

namespace App\Console\Commands;

use App\Enums\CandidateStatus;
use App\Jobs\SendCandidateEmail;
use App\Jobs\SendCandidateSms;
use App\Models\Candidate;
use App\Models\Setting;
use App\Models\Training;
use App\Models\User;
use App\Notifications\TrainingExpiring;
use Illuminate\Console\Command;

class ProcessAutomations extends Command
{
    protected $signature = 'app:process-automations';

    protected $description = 'Run all time-based automations: no-response follow-ups, offer expiry, expiring trainings.';

    public function handle(): int
    {
        $this->info('[1/3] No-response follow-ups...');
        $this->processNoResponseFollowups();

        $this->info('[2/3] Expiring offers...');
        $this->expireOffers();

        $this->info('[3/3] Expiring trainings...');
        $this->checkExpiringTrainings();

        $this->info('Done.');
        return self::SUCCESS;
    }

    // ─── No-response follow-up ────────────────────────────────────────────────

    protected function processNoResponseFollowups(): void
    {
        $followupDays = (int) Setting::get('followup_days', 5);
        $queueDays    = (int) Setting::get('queue_days', 10);

        Candidate::where('status', CandidateStatus::INVITE_SENT->value)
            ->whereNotNull('invite_sent_at')
            ->get()
            ->each(function (Candidate $candidate) use ($followupDays, $queueDays) {
                $daysSince = (int) $candidate->invite_sent_at->diffInDays(now());

                if ($daysSince >= $queueDays) {
                    // 10+ days with no response → move to Queue
                    $candidate->update(['status' => CandidateStatus::QUEUE]);
                    $candidate->activityLogs()->create([
                        'action'      => 'auto_queued',
                        'description' => "Auto-moved to Queue after {$queueDays} days with no response.",
                    ]);
                    $this->line("  Queued: #{$candidate->id} {$candidate->full_name} ({$daysSince}d)");

                } elseif ($daysSince >= $followupDays && $candidate->followup_count === 0) {
                    // 5+ days, first follow-up → SMS preferred, email as fallback
                    if ($candidate->phone) {
                        SendCandidateSms::dispatchSync($candidate, 'sms_followup');
                    } elseif ($candidate->email) {
                        SendCandidateEmail::dispatchSync($candidate, 'followup');
                    }

                    $candidate->update([
                        'followup_count'   => $candidate->followup_count + 1,
                        'last_followup_at' => now(),
                    ]);
                    $candidate->activityLogs()->create([
                        'action'      => 'auto_followup',
                        'description' => "Automated follow-up sent after {$daysSince} days with no response.",
                    ]);
                    $this->line("  Follow-up: #{$candidate->id} {$candidate->full_name} ({$daysSince}d)");
                }
            });
    }

    // ─── Offer expiry ─────────────────────────────────────────────────────────

    protected function expireOffers(): void
    {
        $expired = 0;
        \App\Models\Offer::where('status', 'sent')->get()->each(function ($offer) use (&$expired) {
            if ($offer->isExpired()) {
                $offer->update(['status' => 'expired']);
                $expired++;
            }
        });
        $this->line("  Expired: {$expired} offer(s)");
    }

    // ─── Expiring trainings ───────────────────────────────────────────────────

    protected function checkExpiringTrainings(): void
    {
        $trainings = Training::with('employee')
            ->where('is_completed', false)
            ->whereBetween('due_date', [now(), now()->addDays(30)])
            ->get();

        $notified = 0;
        foreach ($trainings as $training) {
            $userId = $training->employee?->user_id;
            if ($userId) {
                User::find($userId)?->notify(new TrainingExpiring($training));
                $notified++;
            }
        }
        $this->line("  Notified: {$notified} training expiration(s)");
    }
}

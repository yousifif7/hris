<?php

namespace App\Console\Commands;

use App\Enums\CandidateStatus;
use App\Jobs\SendCandidateEmail;
use App\Jobs\SendCandidateSms;
use App\Models\Candidate;
use App\Models\Setting;
use App\Models\Training;
use App\Models\User;
use App\Notifications\AdminActivityNotification;
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

        // Candidates sitting in Pre-Screening with an outstanding invite and no booked interview
        Candidate::where('status', CandidateStatus::PRE_SCREENING->value)
            ->whereNotNull('invite_sent_at')
            ->whereDoesntHave('interviews', fn ($q) => $q->where('status', 'scheduled'))
            ->get()
            ->each(function (Candidate $candidate) use ($followupDays) {
                $daysSince = (int) $candidate->invite_sent_at->diffInDays(now());

                if ($daysSince >= $followupDays && $candidate->followup_count === 0) {
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

        \App\Models\Offer::whereIn('status', ['sent', 'viewed'])
            ->with('candidate')
            ->get()
            ->each(function ($offer) use (&$expired) {
                if (! $offer->isExpired()) {
                    return;
                }

                $offer->update(['status' => 'expired']);
                $expired++;

                $candidate = $offer->candidate;
                if (! $candidate) {
                    return;
                }

                // Log on the candidate timeline
                $candidate->activityLogs()->create([
                    'action'      => 'offer_expired',
                    'description' => "Offer expired with no response after {$offer->deadline_days} day(s).",
                ]);

                // If the candidate is still sitting at the offer stage, log it.
                // We don't bounce them backward — HR decides whether to re-send or close out.
                if ($candidate->status === CandidateStatus::OFFER_LETTER) {
                    $candidate->activityLogs()->create([
                        'action'      => 'offer_expired',
                        'description' => 'Offer expired with no response — candidate still in Offer Letter stage.',
                    ]);
                }

                // Notify all active admins
                $notification = new AdminActivityNotification(
                    '⏰ Offer Expired',
                    "{$candidate->full_name}'s offer expired with no response after {$offer->deadline_days} day(s).",
                    'offer_expired',
                    $candidate->id,
                    $candidate->full_name
                );

                User::where('role', 'admin')
                    ->where('is_active', true)
                    ->get()
                    ->each(fn ($admin) => $admin->notify($notification));

                $this->line("  Expired + notified: #{$candidate->id} {$candidate->full_name}");
            });

        $this->line("  Total expired: {$expired} offer(s)");
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

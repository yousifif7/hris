<?php

namespace App\Jobs;

use App\Models\Candidate;
use App\Models\EmailTemplate;
use App\Models\Message;
use App\Models\Setting;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCandidateSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Candidate $candidate,
        public string $templateSlug,
        public array $extraVars = [],
    ) {}

    public function handle(SmsService $sms): void
    {
        if (! $this->candidate->phone) {
            Log::info("[SendCandidateSms] Candidate #{$this->candidate->id} has no phone number — skipping.");
            return;
        }

        $template = EmailTemplate::where('slug', $this->templateSlug)->first();
        if (! $template) {
            Log::warning("[SendCandidateSms] SMS template '{$this->templateSlug}' not found — skipping.");
            return;
        }

        $vars     = $this->buildVars();
        $rendered = $template->render($vars);
        $body     = $rendered['body'];

        $success = $sms->send($this->candidate->phone, $body);

        if ($success) {
            $from = Setting::get('twilio_from_number', '');

            // Record the sent SMS so HR can see it on the candidate timeline
            Message::create([
                'type'         => 'sms',
                'folder'       => 'sent',
                'candidate_id' => $this->candidate->id,
                'from'         => $from,
                'to'           => $this->candidate->phone,
                'body'         => $body,
                'sent_at'      => now(),
            ]);

            $this->candidate->activityLogs()->create([
                'action'      => 'sms_sent',
                'description' => "Automated SMS sent (template: {$this->templateSlug}).",
            ]);
        }
    }

    protected function buildVars(): array
    {
        $vars = [
            'candidate_name'       => $this->candidate->full_name,
            'candidate_first_name' => $this->candidate->first_name,
            'candidate_last_name'  => $this->candidate->last_name,
            'candidate_email'      => $this->candidate->email ?? '',
            'candidate_phone'      => $this->candidate->phone ?? '',
            'role'                 => $this->candidate->category?->name ?? 'the open position',
            'company_name'         => Setting::get('company_name', 'Wellness Behavioral Health'),
            'hr_name'              => $this->candidate->assignedTo?->full_name ?? 'HR Team',
            'scheduling_link'      => config('app.url') . '/schedule/' . $this->candidate->id,
            'today'                => now()->format('M d, Y'),
        ];

        return array_merge($vars, $this->extraVars);
    }
}

<?php

namespace App\Jobs;

use App\Models\CandidateReference;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReferenceRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public CandidateReference $reference) {}

    public function handle(): void
    {
        $ref       = $this->reference;
        $candidate = $ref->candidate;
        $template  = EmailTemplate::where('slug', 'reference')->first();
        if (!$template) return;

        $rendered = $template->render([
            'reference_name' => $ref->reference_name,
            'candidate_name' => $candidate->full_name,
            'company_name'   => Setting::get('company_name', 'Wellness Behavioral Health'),
            'hr_name'        => $candidate->assignedTo?->full_name ?? 'HR Team',
        ]);

        Mail::raw($rendered['body'], fn($m) =>
            $m->to($ref->reference_email)->subject($rendered['subject'])
        );

        $ref->update(['status' => 'sent', 'sent_at' => now()]);
    }
}

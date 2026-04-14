<?php

namespace App\Jobs;

use App\Models\Candidate;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCandidateEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Candidate $candidate,
        public string $templateSlug,
    ) {}

    public function handle(): void
    {
        if (!$this->candidate->email) return;

        $template = EmailTemplate::where('slug', $this->templateSlug)->first();
        if (!$template) return;

        $vars = [
            'candidate_name'  => $this->candidate->full_name,
            'role'            => $this->candidate->category?->name ?? 'the open position',
            'company_name'    => Setting::get('company_name', 'Wellness Behavioral Health'),
            'hr_name'         => $this->candidate->assignedTo?->full_name ?? 'HR Team',
            'scheduling_link' => config('app.url') . '/schedule/' . $this->candidate->id,
        ];

        $offer = $this->candidate->latestOffer;
        if ($offer) {
            $vars['pay_rate']        = '$' . number_format($offer->pay_rate, 2) . '/' . $offer->pay_type;
            $vars['employment_type'] = $offer->employment_type;
            $vars['location']        = $offer->location ?? 'TBD';
            $vars['start_date']      = $offer->start_date?->format('M d, Y') ?? 'TBD';
        }

        $rendered = $template->render($vars);

        Mail::raw($rendered['body'], function ($message) use ($rendered) {
            $message->to($this->candidate->email)
                    ->subject($rendered['subject']);
        });

        $this->candidate->activityLogs()->create([
            'action'      => 'email_sent',
            'description' => "Email sent: {$this->templateSlug}",
        ]);
    }
}

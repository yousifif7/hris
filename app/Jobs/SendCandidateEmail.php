<?php

namespace App\Jobs;

use App\Models\Candidate;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCandidateEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Candidate $candidate,
        public string $templateSlug,
        public array $extraVars = [],
    ) {}

    public function handle(): void
    {
        if (! $this->candidate->email) return;

        $template = EmailTemplate::where('slug', $this->templateSlug)->first();
        if (! $template) return;

        $offer = $this->candidate->latestOffer;

        $vars = [
            'candidate_name'       => $this->candidate->full_name,
            'candidate_first_name' => $this->candidate->first_name,
            'candidate_last_name'  => $this->candidate->last_name,
            'candidate_email'      => $this->candidate->email ?? '',
            'candidate_phone'      => $this->candidate->phone ?? '',
            'role'                 => $this->candidate->category?->name ?? 'the open position',
            'company_name'         => Setting::get('company_name', 'Wellness Behavioral Health'),
            'hr_name'              => $this->candidate->assignedTo?->full_name ?? 'HR Team',
            'hr_email'             => $this->candidate->assignedTo?->email ?? '',
            'scheduling_link'      => (Setting::get('app_url', config('app.url'))) . '/schedule/' . ($this->candidate->schedule_token ?? $this->candidate->id),
            'prescreening_link'    => $this->candidate->prescreen_token
                ? (Setting::get('app_url', config('app.url'))) . '/prescreen/' . $this->candidate->prescreen_token
                : '',
            'today'                => now()->format('M d, Y'),
        ];

        if ($offer) {
            $vars['offer_pay_rate']        = '$' . number_format($offer->pay_rate, 2) . '/' . $offer->pay_type;
            $vars['offer_employment_type'] = $offer->employment_type;
            $vars['location']              = $offer->location ?? 'TBD';
            $vars['offer_start_date']      = $offer->start_date?->format('M d, Y') ?? 'TBD';
            $vars['offer_orientation_date']= $offer->orientation_date?->format('M d, Y') ?? 'TBD';
            $vars['offer_link']            = $offer->token
                ? (Setting::get('app_url', config('app.url'))) . '/offer/' . $offer->token
                : '';
            // Backwards compat
            $vars['pay_rate']        = $vars['offer_pay_rate'];
            $vars['employment_type'] = $offer->employment_type;
            $vars['start_date']      = $vars['offer_start_date'];
        }

        $vars = array_merge($vars, $this->extraVars);

        $rendered = $template->render($vars);

        $fromEmail = Setting::get('smtp_from_email', config('mail.from.address', 'no-reply@example.com'));
        $fromName  = Setting::get('smtp_from_name', config('mail.from.name', 'HR Team'));

        // Use HTML body if available, else plain text
        $isHtml = ! empty($rendered['body_html']);
        $body   = $isHtml ? $rendered['body_html'] : $rendered['body'];
        $attachments = [];

        if ($this->templateSlug === 'prescreening') {
            $pdfPath = Setting::get('prescreening_pdf_path', 'forms/post-interview-application.pdf');

            if (! empty($pdfPath)) {
                $absolutePath = str_starts_with($pdfPath, DIRECTORY_SEPARATOR)
                    ? $pdfPath
                    : public_path(trim($pdfPath, '/\\'));

                if (is_file($absolutePath)) {
                    $attachments[] = [
                        'path' => $absolutePath,
                        'name' => 'Post-Interview-Application.pdf',
                        'mime' => 'application/pdf',
                    ];
                }
            }
        }

        try {
            MailService::send(
                to: $this->candidate->email,
                subject: $rendered['subject'],
                body: $body,
                isHtml: $isHtml,
                fromEmail: $fromEmail,
                fromName: $fromName,
                attachments: $attachments,
            );

            $this->candidate->activityLogs()->create([
                'action'      => 'email_sent',
                'description' => "Email sent: {$this->templateSlug}",
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("SendCandidateEmail failed [{$this->templateSlug}] to {$this->candidate->email}: " . $e->getMessage());
            throw $e;
        }
    }
}

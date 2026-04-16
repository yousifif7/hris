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
        public array $extraVars = [],
    ) {}

    public function handle(): void
    {
        if (! $this->candidate->email) return;

        $template = EmailTemplate::where('slug', $this->templateSlug)->first();
        if (! $template) return;

        // Apply SMTP settings stored in the database
        $this->applySmtpConfig();

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
            'scheduling_link'      => config('app.url') . '/schedule/' . $this->candidate->id,
            'today'                => now()->format('M d, Y'),
        ];

        if ($offer) {
            $vars['offer_pay_rate']        = '$' . number_format($offer->pay_rate, 2) . '/' . $offer->pay_type;
            $vars['offer_employment_type'] = $offer->employment_type;
            $vars['location']              = $offer->location ?? 'TBD';
            $vars['offer_start_date']      = $offer->start_date?->format('M d, Y') ?? 'TBD';
            $vars['offer_orientation_date']= $offer->orientation_date?->format('M d, Y') ?? 'TBD';
            $vars['offer_link']            = $offer->token
                ? config('app.url') . '/offer/' . $offer->token
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

        Mail::send([], [], function ($mail) use ($rendered, $fromEmail, $fromName, $isHtml) {
            $mail->to($this->candidate->email)
                 ->from($fromEmail, $fromName)
                 ->subject($rendered['subject']);

            if ($isHtml) {
                $mail->setBody($rendered['body_html'], 'text/html');
                $mail->text($rendered['body']);
            } else {
                $mail->text($rendered['body']);
            }
        });

        $this->candidate->activityLogs()->create([
            'action'      => 'email_sent',
            'description' => "Email sent: {$this->templateSlug}",
        ]);
    }

    /**
     * Override Laravel mail config with SMTP settings stored in the database.
     */
    protected function applySmtpConfig(): void
    {
        $host       = Setting::get('smtp_host');
        $port       = Setting::get('smtp_port');
        $username   = Setting::get('smtp_username');
        $password   = Setting::get('smtp_password');
        $encryption = Setting::get('smtp_encryption', 'tls');

        if ($host && $username) {
            config([
                'mail.default'                 => 'smtp',
                'mail.mailers.smtp.host'       => $host,
                'mail.mailers.smtp.port'       => (int) ($port ?: 587),
                'mail.mailers.smtp.encryption' => $encryption,
                'mail.mailers.smtp.username'   => $username,
                'mail.mailers.smtp.password'   => $password,
                'mail.from.address'            => Setting::get('smtp_from_email', config('mail.from.address')),
                'mail.from.name'               => Setting::get('smtp_from_name', config('mail.from.name')),
            ]);
        }
    }
}

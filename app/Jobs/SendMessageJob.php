<?php

namespace App\Jobs;

use App\Models\Candidate;
use App\Models\Message;
use App\Models\Setting;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Message $message) {}

    public function handle(): void
    {
        if ($this->message->type === 'email') {
            $this->sendEmail();
        } elseif ($this->message->type === 'sms') {
            $this->sendSms();
        }
    }

    protected function sendEmail(): void
    {
        if (empty($this->message->to)) {
            Log::warning("[SendMessageJob] No 'to' address for message #{$this->message->id}");
            return;
        }

        // Apply SMTP settings from database
        $this->applySmtpConfig();

        $fromEmail = $this->message->from
            ?: Setting::get('smtp_from_email', config('mail.from.address', 'no-reply@example.com'));
        $fromName  = Setting::get('smtp_from_name', config('mail.from.name', 'HR Team'));

        $body    = $this->message->body;
        $isHtml  = $this->message->is_html;
        $subject = $this->message->subject ?: '(No Subject)';
        $cc      = $this->message->cc;
        $bcc     = $this->message->bcc;

        Mail::send([], [], function ($mail) use ($fromEmail, $fromName, $body, $subject, $isHtml, $cc, $bcc) {
            $mail->to($this->message->to)
                 ->from($fromEmail, $fromName)
                 ->subject($subject);

            if ($cc) $mail->cc($cc);
            if ($bcc) $mail->bcc($bcc);

            if ($isHtml) {
                $mail->setBody($body, 'text/html');
            } else {
                $mail->text($body);
            }
        });

        $this->message->update([
            'folder'  => 'sent',
            'sent_at' => now(),
        ]);

        // Log on linked candidate
        if ($this->message->candidate_id) {
            $this->message->candidate?->activityLogs()->create([
                'user_id'     => $this->message->created_by,
                'action'      => 'email_sent',
                'description' => "Email sent: {$subject}",
            ]);
        }
    }

    protected function sendSms(): void
    {
        if (empty($this->message->to)) {
            Log::warning("[SendMessageJob] No 'to' phone for SMS message #{$this->message->id}");
            return;
        }

        $sms     = app(SmsService::class);
        $success = $sms->send($this->message->to, $this->message->body);

        if ($success) {
            $this->message->update([
                'folder'  => 'sent',
                'sent_at' => now(),
            ]);

            if ($this->message->candidate_id) {
                $this->message->candidate?->activityLogs()->create([
                    'user_id'     => $this->message->created_by,
                    'action'      => 'sms_sent',
                    'description' => "SMS sent to {$this->message->to}",
                ]);
            }
        }
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
                'mail.default'                   => 'smtp',
                'mail.mailers.smtp.host'         => $host,
                'mail.mailers.smtp.port'         => (int) ($port ?: 587),
                'mail.mailers.smtp.encryption'   => $encryption,
                'mail.mailers.smtp.username'     => $username,
                'mail.mailers.smtp.password'     => $password,
                'mail.from.address'              => Setting::get('smtp_from_email', config('mail.from.address')),
                'mail.from.name'                 => Setting::get('smtp_from_name', config('mail.from.name')),
            ]);
        }
    }
}

<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Apply DB SMTP settings to the Laravel mail config and purge the cached
     * mailer so the next send picks up the fresh config.
     *
     * @throws \RuntimeException when no SMTP host/username is configured
     */
    public static function configure(): void
    {
        $host       = Setting::get('smtp_host');
        $username   = Setting::get('smtp_username');

        if (! $host || ! $username) {
            throw new \RuntimeException('SMTP is not configured. Go to Settings → SMTP to add your credentials.');
        }

        $port       = (int) (Setting::get('smtp_port') ?: 587);
        $password   = Setting::get('smtp_password');
        $encryption = strtolower(Setting::get('smtp_encryption', 'tls'));

        config([
            'mail.default'                 => 'smtp',
            'mail.mailers.smtp.transport'  => 'smtp',
            'mail.mailers.smtp.host'       => $host,
            'mail.mailers.smtp.port'       => $port,
            'mail.mailers.smtp.encryption' => ($encryption === 'none' || $encryption === '') ? null : $encryption,
            'mail.mailers.smtp.username'   => $username,
            'mail.mailers.smtp.password'   => $password,
            'mail.from.address'            => Setting::get('smtp_from_email', 'no-reply@example.com'),
            'mail.from.name'               => Setting::get('smtp_from_name', 'HR Team'),
        ]);

        // Purge the cached mailer so it rebuilds with the new config
        Mail::purge('smtp');
    }

    /**
     * Send an email immediately (no queue).
     *
     * @throws \RuntimeException|\Throwable on SMTP misconfiguration or failure
     */
    public static function send(
        string  $to,
        string  $subject,
        string  $body,
        bool    $isHtml    = false,
        ?string $fromEmail = null,
        ?string $fromName  = null,
        ?string $cc        = null,
        ?string $bcc       = null,
        array   $attachments = [],
    ): void {
        static::configure();

        $from      = $fromEmail ?: Setting::get('smtp_from_email', 'no-reply@example.com');
        $name      = $fromName  ?: Setting::get('smtp_from_name', 'HR Team');

        $callback = function ($mail) use ($to, $subject, $from, $name, $cc, $bcc, $attachments) {
            $mail->to($to)
                 ->from($from, $name)
                 ->subject($subject);
            if ($cc)  $mail->cc($cc);
            if ($bcc) $mail->bcc($bcc);

            foreach ($attachments as $attachment) {
                $path = $attachment['path'] ?? null;
                if (! $path || ! is_file($path)) {
                    continue;
                }

                $mail->attach($path, [
                    'as' => $attachment['name'] ?? basename($path),
                    'mime' => $attachment['mime'] ?? 'application/pdf',
                ]);
            }
        };

        $mailer = Mail::mailer('smtp');

        if ($isHtml) {
            $mailer->html($body, $callback);
        } else {
            $mailer->raw($body, $callback);
        }
    }
}

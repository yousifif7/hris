<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

class SmsService
{
    protected ?string $accountSid;
    protected ?string $authToken;
    protected ?string $fromNumber;

    public function __construct()
    {
        $this->accountSid = Setting::get('twilio_account_sid', config('services.twilio.account_sid', ''));
        $this->authToken  = Setting::get('twilio_auth_token', config('services.twilio.auth_token', ''));
        $this->fromNumber = Setting::get('twilio_from_number', config('services.twilio.from', ''));
    }

    /**
     * Send an SMS message via Twilio.
     *
     * @param string $to   Recipient phone (E.164 format recommended, e.g. +12025551234)
     * @param string $body Message body
     * @return bool True on success
     */
    public function send(string $to, string $body): bool
    {
        if (empty($this->accountSid) || empty($this->authToken) || empty($this->fromNumber)) {
            Log::warning('[SmsService] Twilio credentials not configured.');
            return false;
        }

        try {
            // Use Twilio SDK if available, otherwise fall back to HTTP
            if (class_exists(TwilioClient::class)) {
                $client = new TwilioClient($this->accountSid, $this->authToken);
                $client->messages->create($to, [
                    'from' => $this->fromNumber,
                    'body' => $body,
                ]);
            } else {
                // Fallback: Twilio REST API via HTTP
                Http::withBasicAuth($this->accountSid, $this->authToken)
                    ->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json", [
                        'To'   => $to,
                        'From' => $this->fromNumber,
                        'Body' => $body,
                    ])->throw();
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('[SmsService] Send failed: ' . $e->getMessage(), ['to' => $to]);
            return false;
        }
    }

    /**
     * Check whether SMS (Twilio) is configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->accountSid)
            && ! empty($this->authToken)
            && ! empty($this->fromNumber);
    }
}

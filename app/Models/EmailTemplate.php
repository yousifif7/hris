<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'slug', 'name', 'subject', 'body', 'body_html', 'category', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'template_id');
    }

    public function render(array $vars): array
    {
        $subject  = $this->subject;
        $body     = $this->body;
        $bodyHtml = $this->body_html ?? '';

        foreach ($vars as $key => $val) {
            $subject  = str_replace("{{{$key}}}", (string) $val, $subject);
            $body     = str_replace("{{{$key}}}", (string) $val, $body);
            $bodyHtml = str_replace("{{{$key}}}", htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8', false), $bodyHtml);
        }

        return ['subject' => $subject, 'body' => $body, 'body_html' => $bodyHtml];
    }

    /**
     * Return all available template tokens with descriptions.
     */
    public static function availableTokens(): array
    {
        return [
            ['token' => '{{candidate_name}}',        'label' => 'Candidate Full Name',         'group' => 'Candidate'],
            ['token' => '{{candidate_first_name}}',  'label' => 'Candidate First Name',         'group' => 'Candidate'],
            ['token' => '{{candidate_last_name}}',   'label' => 'Candidate Last Name',          'group' => 'Candidate'],
            ['token' => '{{candidate_email}}',       'label' => 'Candidate Email',              'group' => 'Candidate'],
            ['token' => '{{candidate_phone}}',       'label' => 'Candidate Phone',              'group' => 'Candidate'],
            ['token' => '{{role}}',                  'label' => 'Position / Role',              'group' => 'Candidate'],
            ['token' => '{{company_name}}',          'label' => 'Company Name',                 'group' => 'Company'],
            ['token' => '{{hr_name}}',               'label' => 'Assigned HR Name',             'group' => 'HR Staff'],
            ['token' => '{{hr_email}}',              'label' => 'Assigned HR Email',            'group' => 'HR Staff'],
            ['token' => '{{user_name}}',             'label' => 'Logged-in User Full Name',     'group' => 'HR Staff'],
            ['token' => '{{user_email}}',            'label' => 'Logged-in User Email',         'group' => 'HR Staff'],
            ['token' => '{{offer_pay_rate}}',        'label' => 'Offer Pay Rate',               'group' => 'Offer'],
            ['token' => '{{offer_employment_type}}', 'label' => 'Offer Employment Type',        'group' => 'Offer'],
            ['token' => '{{offer_start_date}}',      'label' => 'Offer Start Date',             'group' => 'Offer'],
            ['token' => '{{offer_orientation_date}}','label' => 'Offer Orientation Date',       'group' => 'Offer'],
            ['token' => '{{offer_link}}',            'label' => 'Public Offer Acceptance Link', 'group' => 'Offer'],
            ['token' => '{{scheduling_link}}',       'label' => 'Interview Scheduling Link',    'group' => 'Interview'],
            ['token' => '{{prescreening_link}}',     'label' => 'Public Pre-Screening Form Link', 'group' => 'Interview'],
            ['token' => '{{today}}',                 'label' => "Today's Date",                 'group' => 'System'],
            ['token' => '{{login_url}}',             'label' => 'Employee Portal Login URL',    'group' => 'Onboarding'],
            ['token' => '{{temp_password}}',         'label' => 'Temporary Portal Password',   'group' => 'Onboarding'],
            ['token' => '{{door_code}}',             'label' => 'Building Door Code',           'group' => 'Onboarding'],
            ['token' => '{{wifi_password}}',         'label' => 'Office WiFi Password',         'group' => 'Onboarding'],
        ];
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $body = <<<'PLAIN'
Dear {{candidate_name}},

We are pleased to offer you the position of {{role}} at {{company_name}}.

Offer Details:
• Pay Rate:        {{offer_pay_rate}}
• Employment Type: {{offer_employment_type}}
• Location:        {{location}}
• Start Date:      {{offer_start_date}}
• Orientation Date:{{offer_orientation_date}}

To view and respond to your offer, please visit the link below:
{{offer_link}}

You can ACCEPT or DECLINE directly from that page. Please respond before the deadline.

If you have any questions, don't hesitate to reach out to your HR contact.

Warm regards,
{{hr_name}}
{{company_name}}
PLAIN;

        $bodyHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Job Offer</title></head><body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,sans-serif"><div style="max-width:600px;margin:0 auto;padding:24px 16px"><div style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08)"><div style="background:linear-gradient(135deg,#5ac6cc,#4fbfc7);padding:32px 36px;text-align:center"><p style="margin:0 0 6px;font-size:11px;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,0.8)">Official Job Offer</p><h1 style="margin:0 0 8px;font-size:24px;font-weight:700;color:#ffffff;font-family:Georgia,serif">Congratulations, {{candidate_first_name}}!</h1><p style="margin:0;font-size:14px;color:rgba(255,255,255,0.92)">{{company_name}} is pleased to offer you a position</p></div><div style="padding:32px 36px"><p style="font-size:15px;color:#172b4d;margin:0 0 10px">Dear {{candidate_name}},</p><p style="font-size:14px;color:#5e6c84;line-height:1.7;margin:0 0 24px">We are thrilled to offer you the position of <strong style="color:#172b4d">{{role}}</strong>. Please review the offer details below and let us know your decision at your earliest convenience.</p><div style="background:#f4f5f7;border-radius:8px;padding:20px 24px;margin-bottom:24px"><p style="font-size:11px;text-transform:uppercase;letter-spacing:1.2px;color:#97a0af;font-weight:600;margin:0 0 14px">Offer Details</p><table style="width:100%;border-collapse:collapse"><tr style="border-bottom:1px solid #dfe1e6"><td style="padding:9px 0;font-size:13px;color:#5e6c84">Position</td><td style="padding:9px 0;font-size:14px;font-weight:600;color:#172b4d;text-align:right">{{role}}</td></tr><tr style="border-bottom:1px solid #dfe1e6"><td style="padding:9px 0;font-size:13px;color:#5e6c84">Pay Rate</td><td style="padding:9px 0;font-size:14px;font-weight:600;color:#172b4d;text-align:right">{{offer_pay_rate}}</td></tr><tr style="border-bottom:1px solid #dfe1e6"><td style="padding:9px 0;font-size:13px;color:#5e6c84">Employment Type</td><td style="padding:9px 0;font-size:14px;font-weight:600;color:#172b4d;text-align:right">{{offer_employment_type}}</td></tr><tr style="border-bottom:1px solid #dfe1e6"><td style="padding:9px 0;font-size:13px;color:#5e6c84">Location</td><td style="padding:9px 0;font-size:14px;font-weight:600;color:#172b4d;text-align:right">{{location}}</td></tr><tr style="border-bottom:1px solid #dfe1e6"><td style="padding:9px 0;font-size:13px;color:#5e6c84">Start Date</td><td style="padding:9px 0;font-size:14px;font-weight:600;color:#172b4d;text-align:right">{{offer_start_date}}</td></tr><tr><td style="padding:9px 0;font-size:13px;color:#5e6c84">Orientation Date</td><td style="padding:9px 0;font-size:14px;font-weight:600;color:#172b4d;text-align:right">{{offer_orientation_date}}</td></tr></table></div><p style="font-size:14px;color:#5e6c84;text-align:center;margin:0 0 16px">Click a button below to respond to your offer:</p><div style="text-align:center;margin-bottom:8px"><a href="{{offer_link}}" style="display:inline-block;padding:14px 30px;background:#00875a;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:700;font-size:14px;margin:0 6px 10px">&#10003; Accept Offer</a><a href="{{offer_link}}" style="display:inline-block;padding:14px 30px;background:#fff5f4;color:#de350b;text-decoration:none;border-radius:6px;font-weight:700;font-size:14px;margin:0 6px 10px;border:1px solid rgba(222,53,11,0.25)">&#10007; Decline</a></div><p style="font-size:11px;color:#97a0af;text-align:center;margin:0 0 24px;word-break:break-all">Or copy this link into your browser: <a href="{{offer_link}}" style="color:#5ac6cc;text-decoration:none">{{offer_link}}</a></p><p style="font-size:14px;color:#5e6c84;line-height:1.7;margin:0 0 20px">If you have any questions about this offer, please contact your HR representative.</p><p style="font-size:14px;color:#172b4d;margin:0">Warm regards,<br><strong>{{hr_name}}</strong><br><span style="color:#5e6c84;font-size:13px">{{company_name}}</span></p></div><div style="padding:16px 36px;border-top:1px solid #dfe1e6;text-align:center"><p style="font-size:11px;color:#97a0af;margin:0">This offer letter is confidential and intended solely for {{candidate_name}}. Please do not forward.</p></div></div></div></body></html>';

        DB::table('email_templates')
            ->where('slug', 'offer')
            ->update([
                'body'      => $body,
                'body_html' => $bodyHtml,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('email_templates')
            ->where('slug', 'offer')
            ->update([
                'body'      => "Dear {{candidate_name}},\n\nWe're pleased to offer you {{role}} at {{company_name}}.\n\nPay: {{pay_rate}}\nType: {{employment_type}}\nLocation: {{location}}\nStart: {{start_date}}\n\nPlease respond within 20 days.\n\n{{hr_name}}",
                'body_html' => null,
                'updated_at' => now(),
            ]);
    }
};
